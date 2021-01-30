<?php


namespace App\Service\MovieDB;


use App\Service\Crawler\Model\TorrentModel;
use App\Service\MovieDB\Model\SpaceModel;
use App\Service\MovieDB\Model\StorageModel;
use App\Service\MovieDB\Model\StorageModelArray;

class Database
{
    const TYPE_TORRENT = 'torrent';

    private string $rootDirectory;
    private StorageModelArray $storageList;

    public function setRootDirectory(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
        $this->scanStorage();
    }

    public function insertTorrent(TorrentModel $torrent)
    {
        $uniqid = $torrent->getClient() . '_' . $torrent->getId();
        $data = $torrent->toArray();
        $this->insertFile($uniqid, 'torrent.torrent', $data['file']);
        $this->insertFile($uniqid, 'page.html', $data['html']);
        unset($data['html']);
        unset($data['file']);
        $this->insertValue($uniqid, $data, self::TYPE_TORRENT);
    }

    private function scanStorage()
    {
        $directories = glob($this->rootDirectory . '/*' , GLOB_ONLYDIR);
        $this->storageList = new StorageModelArray();
        foreach ($directories as $directory) {
            $this->storageList[$directory] = new StorageModel($directory);
        }
    }

    private function insertValue(string $uniqid, array $value, string $type): bool
    {
        $storage = $this->getStorage();
        if(!$storage) return false;
        $uniqid = hash('sha1', $uniqid);
        $space = $this->getSpaceForId($storage, $uniqid);
        return $space->storeValue($type, $value);
    }

    private function getStorage()
    {
        $maxSize = 0;
        $biggestStorage = null;
        foreach ($this->storageList as $storage) {
            if($storage->getFreeSpace() > $maxSize) {
                $biggestStorage = $storage;
            }
        }
        return $biggestStorage;
    }

    private function getSpaceForId(StorageModel $storage, string $uniqid): SpaceModel
    {
        return $storage->getSpace($uniqid);
    }

    private function insertFile(string $uniqid, string $name, $file): bool
    {
        $storage = $this->getStorage();
        if(!$storage) return false;
        $uniqid = hash('sha1', $uniqid);
        $space = $this->getSpaceForId($storage, $uniqid);
        return $space->storeFile($name, $file);
    }

}