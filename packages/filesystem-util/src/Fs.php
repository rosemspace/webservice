<?php

namespace Rosem\Utility\Filesystem;

use function dirname;
use function fclose;
use function file_exists;
use function is_resource;

class Fs
{
    //@TODO down search
    //@TODO search from top
    function findUp(string $startDirname, string $searchPath, int $searchMaxLevelUp): ?string
    {
        $filename = null;
        $searchPath = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        while ($searchMaxLevelUp-- >= 0 && $startDirname !== '' && $startDirname !== '.') {
            if ($startDirname === '/') {
                $startDirname = '';
            } elseif ($startDirname === '.') {
                $startDirname = 'TODO_root';
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

    /**
     * Close the stream.
     *
     * @param resource $stream
     */
    public static function closeStream($stream): void
    {
        // Some SDK’s close streams after consuming them, therefore, before calling "fclose" on the resource,
        // check if it’s still valid using "is_resource".
        if (is_resource($stream)) {
            fclose($stream);
        }
    }
}
