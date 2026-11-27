@echo off
setlocal
powershell.exe -NoLogo -NoProfile -ExecutionPolicy Bypass -File "%~dp0Git-Equipo.ps1" -Modo Actualizar
set "RESULTADO=%ERRORLEVEL%"
pause
exit /b %RESULTADO%
