<?php

namespace App\Services;

class Dir
{

    private static $dir = 'upload';

    public static function getUploadDir(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . rtrim(self::$dir, '/') . DIRECTORY_SEPARATOR;
    }

    public static function create(string $path): ?string
    {
        $fullPath = self::getUploadDir() . $path;
        if (file_exists($fullPath)) return $fullPath;

        $fileCreated = mkdir($fullPath, 0777, true);
        return $fileCreated ? $fullPath : null;
    }

    public static function delete(string $path): bool
    {
        $fullPath = self::getUploadDir() . $path;
        if (!file_exists($fullPath)) return false;
        if (is_file($fullPath)) return false;

        foreach (scandir($fullPath) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $fullPath . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::delete(str_replace(self::getUploadDir(), '', $path));
            } else {
                unlink($path);
            }
        }
        return rmdir($fullPath);
    }

    public static function getList(?string $path = null): array
    {
        $result = [];
        $path = $path ? self::getUploadDir() . $path : self::getUploadDir();
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;
            $result[] = $item;
        }
        return $result;
    }

    private static function fullPath(string $path): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/' . rtrim(self::$dir, '/') . '/' . ltrim($path, '/');
    }

}