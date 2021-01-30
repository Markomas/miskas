<?php


namespace App\Service\MovieDB\Model;


use JetBrains\PhpStorm\Pure;
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
        rmdir($this->path);
        if($this->isParentEmpty()) {
            $this->removeParent();
        }
    }

    public function isEmpty(): bool
    {
        return $this->isDirEmpty($this->path);
    }

    private function isParentEmpty()
    {
        return $this->isDirEmpty(dirname($this->path));
    }

    private function removeParent()
    {
        rmdir(dirname($this->path));
    }

    private function isDirEmpty($dir) {
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

    public function getTopFiles(): array
    {
        return array_map('basename', glob($this->path . DIRECTORY_SEPARATOR . "*"));
    }

    public function getPath(): string
    {
        return $this->path;
    }
}