#!/usr/bin/env python3
"""
FastAPI microservice for secure Python code execution
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import docker
import tempfile
import os
import asyncio
import logging
from typing import Optional

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI(
    title="Code Sandbox Execution API",
    description="Secure Python code execution service for Moodle",
    version="1.0.0"
)

# Configure CORS for Moodle
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure with specific Moodle URL in production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class CodeRequest(BaseModel):
    code: str

class GradeRequest(BaseModel):
    student_code: str
    test_code: str

class CodeResponse(BaseModel):
    stdout: str
    stderr: str

class TestResult(BaseModel):
    test_name: str
    passed: bool
    message: Optional[str] = ""

class GradeResponse(BaseModel):
    score: float
    total_tests: int
    passed_tests: int
    results: list[TestResult]

# Initialize Docker client
try:
    docker_client = docker.from_env()
    logger.info("Docker client initialized successfully")
except Exception as e:
    logger.error(f"Failed to initialize Docker client: {e}")
    docker_client = None

@app.get("/")
async def root():
    """Health check endpoint"""
    return {"status": "healthy", "service": "Code Sandbox API"}

@app.post("/execute", response_model=CodeResponse)
async def execute_code(request: CodeRequest):
    """Execute Python code in a secure Docker container"""
    
    if not docker_client:
        raise HTTPException(status_code=503, detail="Docker service unavailable")
    
    # Create temporary file
    with tempfile.NamedTemporaryFile(mode='w', suffix='.py', delete=False) as f:
        f.write(request.code)
        temp_file = f.name
    
    try:
        # Prepare Docker command
        container = docker_client.containers.run(
            "python:3.8-slim",
            f"python /code/{os.path.basename(temp_file)}",
            volumes={os.path.dirname(temp_file): {'bind': '/code', 'mode': 'ro'}},
            working_dir="/code",
            mem_limit="128m",
            nano_cpus=1000000000,  # 1 CPU
            network_mode="none",  # No network access
            remove=True,
            stdout=True,
            stderr=True,
            detach=True
        )
        
        # Wait for completion with timeout
        try:
            result = container.wait(timeout=10)
            logs = container.logs(stdout=True, stderr=True)
            
            # Parse stdout and stderr
            stdout = ""
            stderr = ""
            
            # Docker logs come as bytes
            if isinstance(logs, bytes):
                output = logs.decode('utf-8', errors='replace')
                # Simple heuristic: if exit code is non-zero, it's likely stderr
                if result['StatusCode'] != 0:
                    stderr = output
                else:
                    stdout = output
            
            return CodeResponse(stdout=stdout, stderr=stderr)
            
        except Exception as timeout_error:
            try:
                container.stop()
                container.remove()
            except:
                pass
            return CodeResponse(
                stdout="",
                stderr="Error: Code execution timed out (10 second limit)"
            )
        
    except docker.errors.ContainerError as e:
        # Container exited with non-zero status
        stdout = e.stdout.decode('utf-8') if e.stdout else ""
        stderr = e.stderr.decode('utf-8') if e.stderr else str(e)
        return CodeResponse(stdout=stdout, stderr=stderr)
        
    except docker.errors.ImageNotFound:
        raise HTTPException(status_code=500, detail="Python Docker image not found")
        
    except Exception as e:
        logger.error(f"Execution error: {e}")
        raise HTTPException(status_code=500, detail=str(e))
        
    finally:
        # Clean up temp file
        try:
            os.unlink(temp_file)
        except:
            pass

@app.post("/grade", response_model=GradeResponse)
async def grade_code(request: GradeRequest):
    """Grade student code against unit tests"""
    
    if not docker_client:
        raise HTTPException(status_code=503, detail="Docker service unavailable")
    
    # Create temp directory for files
    with tempfile.TemporaryDirectory() as temp_dir:
        # Write student code
        student_file = os.path.join(temp_dir, "solution.py")
        with open(student_file, 'w') as f:
            f.write(request.student_code)
        
        # Write test code
        test_file = os.path.join(temp_dir, "test_solution.py")
        with open(test_file, 'w') as f:
            f.write(request.test_code)
        
        # Create test runner script
        runner_file = os.path.join(temp_dir, "runner.py")
        with open(runner_file, 'w') as f:
            f.write('''
import json
import unittest
import sys
from io import StringIO

# Load the test module
loader = unittest.TestLoader()
suite = loader.loadTestsFromName('test_solution')

# Run tests and capture results
stream = StringIO()
runner = unittest.TextTestRunner(stream=stream, verbosity=2)
result = runner.run(suite)

# Extract test results
test_results = []
for test, error in result.failures + result.errors:
    test_name = test._testMethodName
    test_results.append({
        "test_name": test_name,
        "passed": False,
        "message": error
    })

for test in result.testsRun * [None]:  # Placeholder for successful tests
    if test and test._testMethodName not in [r["test_name"] for r in test_results]:
        test_results.append({
            "test_name": test._testMethodName,
            "passed": True,
            "message": ""
        })

# Get all test names
all_tests = []
for test in suite:
    if hasattr(test, '_testMethodName'):
        all_tests.append(test._testMethodName)
    else:
        for subtest in test:
            if hasattr(subtest, '_testMethodName'):
                all_tests.append(subtest._testMethodName)

# Mark successful tests
for test_name in all_tests:
    if test_name not in [r["test_name"] for r in test_results]:
        test_results.append({
            "test_name": test_name,
            "passed": True,
            "message": ""
        })

# Calculate score
total_tests = len(all_tests)
passed_tests = len([r for r in test_results if r["passed"]])
score = passed_tests / total_tests if total_tests > 0 else 0

# Output JSON result
output = {
    "score": score,
    "total_tests": total_tests,
    "passed_tests": passed_tests,
    "results": test_results
}

print(json.dumps(output))
''')
        
        try:
            # Run test runner in container
            container = docker_client.containers.run(
                "python:3.8-slim",
                "python /code/runner.py",
                volumes={temp_dir: {'bind': '/code', 'mode': 'ro'}},
                working_dir="/code",
                mem_limit="256m",
                nano_cpus=1000000000,  # 1 CPU
                network_mode="none",
                remove=True,
                stdout=True,
                stderr=True,
                detach=True
            )
            
            # Wait for completion
            try:
                result = container.wait(timeout=15)
                logs = container.logs(stdout=True, stderr=False)
                
                # Parse JSON output
                if isinstance(logs, bytes):
                    logs = logs.decode('utf-8', errors='replace')
                
                # Find JSON output (last line should be JSON)
                lines = logs.strip().split('\n')
                json_output = None
                
                for line in reversed(lines):
                    try:
                        json_output = json.loads(line)
                        break
                    except:
                        continue
                
                if json_output:
                    return GradeResponse(**json_output)
                else:
                    # Fallback if JSON parsing fails
                    return GradeResponse(
                        score=0.0,
                        total_tests=0,
                        passed_tests=0,
                        results=[TestResult(
                            test_name="error",
                            passed=False,
                            message="Failed to parse test results"
                        )]
                    )
                    
            except Exception as timeout_error:
                try:
                    container.stop()
                    container.remove()
                except:
                    pass
                return GradeResponse(
                    score=0.0,
                    total_tests=0,
                    passed_tests=0,
                    results=[TestResult(
                        test_name="timeout",
                        passed=False,
                        message="Test execution timed out (15 second limit)"
                    )]
                )
                
        except docker.errors.ContainerError as e:
            stderr = e.stderr.decode('utf-8') if e.stderr else str(e)
            return GradeResponse(
                score=0.0,
                total_tests=0,
                passed_tests=0,
                results=[TestResult(
                    test_name="error",
                    passed=False,
                    message=f"Container error: {stderr}"
                )]
            )
            
        except Exception as e:
            logger.error(f"Grading error: {e}")
            raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)