@echo off

:: reset changes
git reset --hard

:: pull for update
git pull
git submodule update --init

:: delete setup ?
set /p yn="Do you want to keep setup folder? [Y]es [N]o "
if /I "%yn%" NEQ "y" (
   echo "Remove setup folder"
   rmdir /s /q "setup/" 2> NUL
   del setup.sql
   del setup.conf
)
