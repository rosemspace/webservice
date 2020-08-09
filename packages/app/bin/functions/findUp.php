<?php

namespace Rosem\Component\App;

use function dirname;
use function file_exists;
use function function_exists;

if (!function_exists('findUp')) {
    //@TODO down search
    //@TODO search from top
    function findUp(string $startDirname, string $searchPath, int $searchMaxLevelUp): ?string
    {
        $filename = null;
        $searchPath = DIRECTORY_SEPARATOR . trim($searchPath, DIRECTORY_SEPARATOR);

        while ($searchMaxLevelUp-- >= 0 && $startDirname !== '' && $startDirname !== '.') {
            if ($startDirname === '/') {
                $startDirname = '';
            } elseif ($startDirname === '.') {
                $startDirname = 'TODO_root'; // TODO ?
            }

            $possibleFilename = "$startDirname$searchPath";

            if (file_exists($possibleFilename)) {
                $filename = $possibleFilename;

                break;
            }

            if ($possibleFilename === $searchPath) {
                break;
            }

            $startDirname = dirname($startDirname);
        }

        return $filename;
    }
}
