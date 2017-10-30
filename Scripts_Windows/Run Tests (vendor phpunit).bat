@echo off
cls
cd ".."

call cmd /C "vendor\bin\phpunit.bat"

pause