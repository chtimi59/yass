@echo off
setlocal 
pushd %~dp0

:: windows double click implies to get a pause to see the errors
set EXPLORERCALL=0
if %0 == "%~0" ( set EXPLORERCALL=1 )

:: get current version
if not exist "VERSION" call :error_prompt "VERSION file is missing" && exit /b 1
set /p ver=<"VERSION"
echo Yass current version: %ver%

:: reset changes
git reset --hard
if %errorlevel% neq 0 call :error_prompt && exit /b 1
:: pull for update
git pull
if %errorlevel% neq 0 call :error_prompt && exit /b 1
:: update submodules also
git submodule update --init
if %errorlevel% neq 0 call :error_prompt && exit /b 1

set /p yn="Do you want to keep setup folder? [Y]es [N]o"
if /I "%yn%" NEQ "y" (
   echo "Remove setup folder"
   rmdir /s /q "setup/" 2> NUL
   del setup.sql
   del setup.conf
)

php .migration %ver%
if %errorlevel% neq 0 call :error_prompt && exit /b 1
echo.

:: get current version
if not exist "VERSION" call :error_prompt "VERSION file is missing" && exit /b 1
set /p ver=<"VERSION"
echo Yass new current version: %ver%

:: ----------------------
goto :success_end

:success_end
if %EXPLORERCALL% == 1 ( pause )
exit /b 0


:error_prompt
if "%~1" neq "" echo %~1
if %EXPLORERCALL% == 1 ( pause )
exit /b 0


