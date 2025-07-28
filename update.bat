@echo off
cd /d "%~dp0"
echo جاري تحديث البرنامج من Git...
git pull origin main
pause