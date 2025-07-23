FROM python:3.8-slim

WORKDIR /app

# Install Docker CLI (needed to communicate with Docker daemon)
RUN apt-get update && \
    apt-get install -y docker.io && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy requirements and install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application
COPY main.py .

# Note: Running as root is required for Docker socket access
# Security is maintained through container restrictions and network isolation

EXPOSE 8000

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]