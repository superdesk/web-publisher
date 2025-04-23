#!/bin/sh

# Debug information
echo "Starting WebSocket server..."
echo "Current directory: $(pwd)"
echo "PHP version: $(php -v)"
echo "Extensions loaded: $(php -m)"

# Run WebSocket server
cd /var/www/publisher && php bin/console gos:websocket:server --host=0.0.0.0 --port=8080 