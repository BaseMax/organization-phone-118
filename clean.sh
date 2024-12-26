#!/bin/bash

if [ -f "meili_data/.keep" ]; then
    echo "Keeping .keep file"
    
    find meili_data -mindepth 1 ! -name ".keep" -exec rm -rf {} +
    
    echo "Cleaned up meili_data directory."
else
    echo ".keep file not found, aborting clean."
fi
