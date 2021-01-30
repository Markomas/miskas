<?php


namespace App\Service\MovieDB\Model;


use JetBrains\PhpStorm\Pure;

class StorageModel
{
    private string $path;

    public function __construct(string $path){
        $this->path = $path;
    }

    #[Pure] public function getFreeSpace(): float {
        return disk_free_space($this->path);
    }

    public function getSpace(string $uniqid): SpaceModel {
        $uniqid = substr_replace($uniqid, DIRECTORY_SEPARATOR, 2, 0);
        $path = $this->path . DIRECTORY_SEPARATOR . $uniqid;
        if(!file_exists($path)) {
            mkdir($path, 0744, true);
        }
        return new SpaceModel($path);
    }

    #[Pure] public function getAll(): \Generator
    {
        $directories = glob($this->path . '/*' , GLOB_ONLYDIR);

        foreach($directories as $directory) {
            $subDirectories = glob($directory . '/*' , GLOB_ONLYDIR);
            foreach($subDirectories as $subDirectory) {
                yield new SpaceModel($subDirectory);
            }
        }
    }
}