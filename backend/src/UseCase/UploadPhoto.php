<?php

namespace App\UseCase;

use App\Services\Dir;
use App\Services\File;
use DateTime;

class UploadPhoto
{
    public function load(): void
    {
        $name = new DateTime()->getTimestamp();
        $neutralDir = Dir::create($name);

        $neutralFileName = $_FILES['neutral']['tmp_name'];
        $anxiousFileTmpPath = $_FILES['anxious']['tmp_name'];

        File::move($anxiousFileTmpPath, "$neutralDir/anxious");
        File::move($neutralFileName, "$neutralDir/neutral");
    }

    public function get(): array {
        $result = [];
        foreach (Dir::getList() as $dir) {
            $result[$dir] = array_map(fn($item) => "/upload/$dir/$item", Dir::getList($dir));
        }
        return $result;
    }

    public function delete(string|int $id): void {
        Dir::delete($id);
    }
}