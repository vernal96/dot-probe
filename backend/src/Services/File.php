<?php

namespace App\Services;

class File
{

    public static function move(string $pathFrom, string $pathTo): bool
    {
        $dir = dirname($pathTo);
        if (!is_dir($dir)) {
            mkdir($dir, 7777, true);
        }

        return file_exists($pathFrom) && rename($pathFrom, $pathTo) && chmod($pathTo, 0777);
    }

}