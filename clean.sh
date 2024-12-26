#!/bin/bash

if [ -f "meili_data/" ]; then
    echo "Keeping .keep file"

    rm -rf meili_data/data.ms
    rm -rf meili_data/dumps
    
    echo "Cleaned up meili_data directory."
else
    echo "meili_data directory not found, aborting clean."
fi
