# Live Code Sandbox for Moodle 3.5

## The Problem

Traditional programming education in Moodle was limited to static content and file submissions. Students had to:
- Write code in external IDEs or text editors
- Submit files for manual grading
- Wait for instructor feedback
- Switch between multiple tools to test their code
- Deal with environment setup issues on their local machines

This fragmented workflow made it difficult for students to learn programming effectively, especially beginners who struggled with tool setup and configuration.

## The New Feature

The Live Code Sandbox brings interactive programming directly into Moodle courses. Students can write, execute, and test Python code in their browser with immediate feedback. Key features include:

- **In-Browser Code Editor**: Syntax-highlighted editor powered by CodeMirror
- **Instant Execution**: Click "Run" to execute code and see results immediately
- **Secure Environment**: Code runs in isolated Docker containers with resource limits
- **Real-Time Output**: See both standard output and error messages
- **Persistent Code**: Student work is automatically saved and restored
- **Starter Code**: Instructors can provide template code to get students started

### Key Benefits:

- **Zero Setup**: Students need only a web browser - no local development environment
- **Immediate Feedback**: See results instantly without submission delays
- **Safe Experimentation**: Isolated execution prevents system damage
- **Consistent Environment**: All students use the same Python version and libraries
- **Integrated Learning**: Code practice happens within the course context

## Implementation Notes

The feature consists of two main components:

### 1. Moodle Activity Module (`mod_codesandbox`)

- Standard Moodle activity that instructors add to courses
- Stores activity configuration and student code in the database
- Provides the web interface with CodeMirror editor
- Handles AJAX communication with the execution service

### 2. Docker Execution Service (FastAPI)

- Separate microservice running on port 8080
- Receives code via REST API
- Creates temporary Docker containers for execution
- Returns output to the Moodle frontend
- Enforces security limits (memory, CPU, timeout)

### Security Architecture

- **Container Isolation**: Each code execution runs in a fresh container
- **Resource Limits**: 128MB memory, 50% CPU, 10-second timeout
- **No Network Access**: Containers cannot access external resources
- **Read-Only Filesystem**: Prevents persistent changes
- **Automatic Cleanup**: Containers are destroyed after execution

For detailed Docker setup instructions, see [DOCKER.md](DOCKER.md).

## How to Test

### 1. Start the Docker Service
First, ensure the code execution service is running:

```bash
cd codesandbox-api
docker-compose up -d
```

Verify it's working:
```bash
curl http://localhost:8080/
# Should return: {"status":"healthy","service":"Code Sandbox API"}
```

### 2. Add Code Sandbox to a Course
1. Log in as a teacher
2. Turn editing on in your course
3. Click "Add an activity or resource"
4. Select "Code Sandbox"
5. Configure the activity:
    - **Name**: "Python Practice"
    - **Description**: "Practice basic Python programming"
    - **Starter code** (optional):

            # Write a function that adds two numbers
            def add(a, b):
                # Your code here
                pass
    
            # Test your function
            print(add(2, 3))

6. Save and return to course

### 3. Test as a Student
1. Log in as a student enrolled in the course
2. Click on "Python Practice"
3. You should see:
    - Code editor with syntax highlighting
    - "Run Code" button
    - Output panel (initially empty)

### 4. Write and Execute Code
Replace the starter code with:
```python
def add(a, b):
    return a + b

# Test the function
print("2 + 3 =", add(2, 3))
print("Hello from Moodle!")

# Try a loop
for i in range(5):
    print(f"Count: {i}")
```

Click "Run Code" and verify:

- Loading spinner appears
- Output shows all print statements
- No errors occur

### 5. Test Error Handling
Try code with an error:
```python
print("This line works")
print(undefined_variable)  # This will error
```

Verify:

- First line output appears
- Error message shows in red
- Editor remains functional

### 6. Test Resource Limits
Try an infinite loop:
```python
while True:
    print("Infinite loop")
```

Verify:

- Execution stops after ~10 seconds
- Appropriate timeout message appears
- System remains responsive

### Quick Test Checklist

- [ ] Docker service is running on port 8080
- [ ] Activity appears in course
- [ ] Code editor loads with syntax highlighting
- [ ] Starter code is pre-populated
- [ ] Run button executes code
- [ ] Output displays correctly
- [ ] Errors show in red
- [ ] Resource limits work (timeout, memory)
- [ ] Code persists when leaving and returning


### Common Issues

- **"Could not connect to execution service"**: Check Docker is running
- **No output after clicking Run**: Check browser console for errors
- **Port 8080 in use**: Change port in docker-compose.yml
- **Permission denied**: Ensure user has Docker permissions

For comprehensive testing procedures, see [TESTS.md](TESTS.md).

The Live Code Sandbox transforms Moodle into an interactive programming learning environment, removing barriers and enabling students to focus on learning to code rather than configuring tools.
