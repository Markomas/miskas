<?php


namespace App\Service\MovieDB\Model;


use Normalizer;

class SpaceModel
{
    private string $path;

    public function __construct(string $path){
        $this->path = $path;
    }

    public function storeValue(string $name, array $value): bool
    {
        $name = preg_replace('/[^a-zA-Z0-9_.]/', '', $name);
        $file = $this->path . DIRECTORY_SEPARATOR . $name . '.json';
        $data = json_encode($value, JSON_PRETTY_PRINT);
        if(!$data) return false;
        return file_put_contents($file, $data);
    }

    public function storeFile(string $name, $file): bool
    {
        return file_put_contents($this->path . DIRECTORY_SEPARATOR . $name, $file);
    }

    public function remove() {

    }

    public function isEmpty($dir): bool
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }
}