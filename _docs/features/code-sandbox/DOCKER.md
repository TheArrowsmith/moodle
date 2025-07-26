# Docker Setup for Code Sandbox API

This guide explains how to set up and run the Docker-based code execution service required for the Moodle Code Sandbox module.

## Prerequisites

- Docker installed on your system
- Docker Compose installed
- Port 8080 available on your machine (maps to container port 8000)

## Directory Structure

The code execution API is located in:
```
/codesandbox-api/
├── Dockerfile
├── docker-compose.yml
├── main.py
└── requirements.txt
```

## Starting the Service

### 1. Navigate to the API Directory

```bash
cd codesandbox-api
```

### 2. Create Docker Network (First Time Only)

The service uses a Docker network for communication. Create it once:

```bash
docker network create moodle-network
```

### 3. Build and Start the Service

Use Docker Compose to build and start the service:

```bash
docker-compose up -d --build
```

Options:
- `-d` runs in detached mode (background)
- `--build` ensures the image is rebuilt with latest code changes

### 4. Verify Service is Running

Check if the container is running:

```bash
docker ps | grep codesandbox
```

You should see output like:
```
8fdd7301f68e   codesandbox-api-codesandbox-api   "uvicorn main:app --…"   Up 7 seconds   0.0.0.0:8080->8000/tcp
```

### 5. Test the Service

Test the health endpoint:

```bash
curl http://localhost:8080/
```

Expected response:
```json
{"status":"healthy","service":"Code Sandbox API"}
```

## Managing the Service

### View Logs

To see what's happening inside the container:

```bash
docker logs codesandbox-api-codesandbox-api-1
```

Follow logs in real-time:

```bash
docker logs -f codesandbox-api-codesandbox-api-1
```

### Stop the Service

```bash
docker-compose down
```

### Restart the Service

```bash
docker-compose restart
```

### Rebuild After Code Changes

If you modify the Python code:

```bash
docker-compose down
docker-compose up -d --build
```

## API Endpoints

Once running, the service provides:

- `GET /` - Health check
- `POST /execute` - Execute Python code
- `POST /grade` - Run unit tests against code

### Example: Testing Code Execution

```bash
curl -X POST http://localhost:8080/execute \
  -H "Content-Type: application/json" \
  -d '{"code": "print(\"Hello from Docker!\")"}'
```

## Troubleshooting

### Port Already in Use

If port 8080 is already taken:

1. Find what's using it:
   ```bash
   lsof -i :8080
   ```

2. Either stop that service or change the port in `docker-compose.yml`:
   ```yaml
   ports:
     - "8081:8000"  # Use port 8081 instead
   ```

### Permission Denied Errors

If you get Docker permission errors:

```bash
sudo usermod -aG docker $USER
```

Then log out and back in.

### Container Keeps Restarting

Check the logs for errors:

```bash
docker logs codesandbox-api-codesandbox-api-1
```

Common issues:
- Python syntax errors in main.py
- Missing dependencies in requirements.txt
- Docker daemon not running

### Cannot Connect to Docker Daemon

Ensure Docker is running:

```bash
sudo systemctl start docker  # Linux
# or
sudo service docker start    # Ubuntu/Debian
```

## Security Notes

The service runs code in isolated Docker containers with:
- Memory limit: 128MB per execution
- CPU limit: 1 CPU core
- No network access
- 10-second timeout
- Read-only file system

## Integration with Moodle

Once the service is running, configure Moodle:

1. Ensure Moodle can reach `http://localhost:8080`
2. If Moodle runs in Docker, use `http://host.docker.internal:8080` (Mac/Windows) or the host IP
3. For production, configure proper domain and SSL

## Production Considerations

For production deployment:

1. Use environment variables for configuration
2. Set specific CORS origins instead of "*"
3. Add authentication tokens
4. Use a reverse proxy (nginx) with SSL
5. Monitor resource usage
6. Set up log rotation
7. Use Docker Swarm or Kubernetes for scaling

## Quick Reference

```bash
# Start
cd codesandbox-api && docker-compose up -d

# Stop
docker-compose down

# Logs
docker logs -f codesandbox-api-codesandbox-api-1

# Rebuild
docker-compose up -d --build

# Check status
docker ps | grep codesandbox
```