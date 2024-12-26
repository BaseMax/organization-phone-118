@echo off

if exist "meili_data\.keep" (
    echo Keeping .keep file

    for /d %%D in (meili_data\*) do rmdir /s /q "%%D"
    del /q meili_data\*
    echo Cleaned up meili_data directory.
) else (
    echo .keep file not found, aborting clean.
)
