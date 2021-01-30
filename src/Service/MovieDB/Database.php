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
    /**
     * @var Indexer
     */
    private Indexer $indexer;

    public function __construct(Indexer $indexer) {
        $this->indexer = $indexer;
    }

    public function setRootDirectory(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
        $this->scanStorage();
    }

    public function insertTorrent(TorrentModel $torrent)
    {
        $uniqid = $torrent->getClient() . '_' . $torrent->getId();
        $uniqid = hash('sha256', $uniqid);
        $storage = $this->getStorage();
        if(!$storage) return false;
        $space = $this->getSpaceForId($storage, $uniqid);

        $data = $torrent->toArray();
        $this->insertFile($space, 'torrent.torrent', $data['file']);
        $this->insertFile($space, 'page.html', $data['html']);
        unset($data['html']);
        unset($data['file']);
        $this->insertValue($space, $data, self::TYPE_TORRENT);
        $this->indexer->add($space);
    }

    private function scanStorage()
    {
        $directories = glob($this->rootDirectory . '/*' , GLOB_ONLYDIR);
        $this->storageList = new StorageModelArray();
        foreach ($directories as $directory) {
            $this->storageList[$directory] = new StorageModel($directory);
        }
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

    private function insertFile(SpaceModel $space, string $name, $file): bool
    {
        return $space->storeFile($name, $file);
    }

    private function insertValue(SpaceModel $space, array $value, string $type): bool
    {
        return $space->storeValue($type, $value);
    }


}