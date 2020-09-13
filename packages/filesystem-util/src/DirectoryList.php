<?php

declare(strict_types=1);

namespace Rosem\Component\Filesystem;

use InvalidArgumentException;

use function dirname;
use function fclose;
use function fopen;
use function fwrite;
use function getcwd;
use function preg_replace;
use function realpath;
use function rtrim;
use function str_replace;
use function stream_get_meta_data;
use function strip_tags;
use function strlen;
use function sys_get_temp_dir;
use function tempnam;
use function tmpfile;
use function unlink;

class DirectoryList
{
    //app_path
    //base_path
    //config_path
    //database_path
    //public_path
    //resource_path
    //storage_path

    /**
     * System base temporary directory.
     */
    public const SYSTEM_TEMP = 'sys_tmp';

    /**
     * The application root directory.
     */
    private string $root;

    /**
     * The system temporary directory.
     */
    private string $temp;

    public function getRoot(): string
    {
        // Try to guess root
        if (! isset($this->root)) {
            if (PHP_SAPI === 'cli') {
                // Go above "bin/cli" file
                $this->root = dirname(
                    rtrim($_SERVER['PWD'] ?: getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .
                    preg_replace(
                        '~^\.?' . DIRECTORY_SEPARATOR . '+|' . DIRECTORY_SEPARATOR . '+$~',
                        '',
                        $_SERVER['SCRIPT_NAME']
                    ),
                    2
                );
            } else {
                // Go above "public" directory
                $this->root = dirname($_SERVER['DOCUMENT_ROOT'] ?: getcwd());
            }

            $this->root = strip_tags($this->root);
        }

        return $this->root;
    }

    public function getSystemTempDirname(): string
    {
        if (! isset($this->temp)) {
            $this->temp = realpath(rtrim(sys_get_temp_dir(), '\\/'));
        }

        return $this->temp;
    }

    public function getPath(string $directory): ?string
    {
        return '';
    }

    public function newTempFilename(string $prefix = '', ?string $directory = null)
    {
        if (strlen($prefix) > 3) {
            throw new InvalidArgumentException('Prefix is too long; use 3 ASCII characters maximum.');
        }

        return tempnam($directory ?? $this->temp, $prefix);
    }

    public function newTempFile()
    {
        // TODO $path = stream_get_meta_data($file)['uri'];

        return tmpfile();
    }

    /**
     * Converts slashes in path to the current style.
     */
    public function normalizePath(string $path): string
    {
        return str_replace(DIRECTORY_SEPARATOR === '/' ? '\\' : '/', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Converts slashes in path to a conventional unix-style.
     */
    public function unifyPath(string $path): string
    {
        return DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', $path) : $path;
    }

    public function newTempFilename_($tempFilename): void
    {
        $handle = fopen($tempFilename, 'w+b');
        fwrite($handle, 'Write into the temporary file');
        fclose($handle);

        // todo

        unlink($tempFilename);
    }
}
