@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../rosem/app/Resource/bin/rosem
php "%BIN_TARGET%" %*
