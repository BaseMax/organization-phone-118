#!/bin/bash

echo "Starting the process..."

echo "Sleeping for 5 seconds to ensure all services are ready..."
sleep 5

echo "Running the import script..."
php data/import.php

if [ $? -eq 0 ]; then
    echo "Import script succeeded. Starting PHP servers..."

    echo "Starting PHP server for static files on port 8080..."
    php -S 0.0.0.0:8080 -t /app index.html &

    echo "Starting PHP server for API on port 8000..."
    php -S 0.0.0.0:8000 -t /app api.php

else
    echo "Import script failed, exiting..."
    exit 1
fi
