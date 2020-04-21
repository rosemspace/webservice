<?php

if (!function_exists('findAutoloadFile')) {
    function findAutoloadFile(string $dir, int $searchMaxLevelUp): string
    {
        $autoloadFile = null;
        $autoloadFileTail = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        while ($searchMaxLevelUp-- >= 0) {
            $file = "$dir$autoloadFileTail";

            if (file_exists($file)) {
                $autoloadFile = $file;

                break;
            }

            $dir = dirname($dir);
        }

        return $autoloadFile;
    }
}
