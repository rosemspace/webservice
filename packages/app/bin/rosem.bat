@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../rosem/app/bin/rosem
php "%BIN_TARGET%" %*
