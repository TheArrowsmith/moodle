#!/usr/bin/env python3
"""
Test script to verify all languages work correctly with the API
"""

import requests
import json

API_URL = "http://localhost:8080"

# Test cases for each language
test_cases = [
    {
        "language": "python",
        "code": """
print("Hello from Python!")
x = 5
y = 10
print(f"The sum of {x} and {y} is {x + y}")

# Test a simple function
def greet(name):
    return f"Hello, {name}!"

print(greet("World"))
"""
    },
    {
        "language": "ruby",
        "code": """
puts "Hello from Ruby!"
x = 5
y = 10
puts "The sum of #{x} and #{y} is #{x + y}"

# Test a simple method
def greet(name)
  "Hello, #{name}!"
end

puts greet("World")
"""
    },
    {
        "language": "elixir",
        "code": """
IO.puts "Hello from Elixir!"
x = 5
y = 10
IO.puts "The sum of #{x} and #{y} is #{x + y}"

# Test a simple function
greet = fn name -> "Hello, #{name}!" end

IO.puts greet.("World")
"""
    }
]

def test_language(language, code):
    """Test a specific language"""
    print(f"\n{'='*50}")
    print(f"Testing {language.upper()}")
    print(f"{'='*50}")
    
    try:
        response = requests.post(
            f"{API_URL}/execute",
            json={"code": code, "language": language},
            timeout=15
        )
        
        if response.status_code == 200:
            result = response.json()
            print(f"Status: SUCCESS")
            print(f"\nOutput:")
            print(result.get('stdout', '(no output)'))
            if result.get('stderr'):
                print(f"\nErrors:")
                print(result['stderr'])
        else:
            print(f"Status: FAILED (HTTP {response.status_code})")
            print(f"Error: {response.text}")
            
    except requests.exceptions.ConnectionError:
        print(f"Status: FAILED")
        print(f"Error: Could not connect to API at {API_URL}")
    except Exception as e:
        print(f"Status: FAILED")
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    print("Code Sandbox Language Test")
    print(f"API URL: {API_URL}")
    
    # First check if API is running
    try:
        health = requests.get(f"{API_URL}/", timeout=5)
        if health.status_code == 200:
            print(f"API Status: {health.json()}")
        else:
            print("API is not responding correctly")
            exit(1)
    except:
        print(f"ERROR: Cannot connect to API at {API_URL}")
        print("Make sure the API is running (docker-compose up)")
        exit(1)
    
    # Test each language
    for test in test_cases:
        test_language(test["language"], test["code"])
    
    print(f"\n{'='*50}")
    print("All tests completed!")