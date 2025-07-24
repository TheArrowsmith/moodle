#!/bin/bash
# Script to pull all required Docker images for the code sandbox

echo "Pulling Docker images for Code Sandbox..."

echo "Pulling Python 3.8..."
docker pull python:3.8-slim

echo "Pulling Ruby 3.0..."
docker pull ruby:3.0-slim

echo "Pulling Elixir 1.13..."
docker pull elixir:1.13-slim

echo "All images pulled successfully!"
echo ""
echo "Available images:"
docker images | grep -E "(python:3.8-slim|ruby:3.0-slim|elixir:1.13-slim)"