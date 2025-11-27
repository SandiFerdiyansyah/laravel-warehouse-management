#!/bin/bash
# Fetch login page HTML and check structure

echo "Fetching login page HTML..."
curl -s http://127.0.0.1:8000/login | head -100
