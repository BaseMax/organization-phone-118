@echo off

IF EXIST "meili_data\" (
    echo Keeping .keep file

    del /f /q meili_data\data.ms
    rmdir /s /q meili_data\dumps

    echo Cleaned up meili_data directory.
) ELSE (
    echo meili_data directory not found, aborting clean.
)
