@echo off
set PHPCOMMAND=C:\Apache2.2\php5.2.6\php.exe
set FILE=./engine/scripts/scripts.php

if exist %FILE% goto exists
echo "This current dir is not a valid cube project."

:exists
%PHPCOMMAND% %FILE% %0 %*
