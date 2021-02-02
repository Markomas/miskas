<?php


namespace App\Service\MovieDB;


use App\Entity\StorageSpace;
use App\Service\Crawler\Model\TorrentModel;
use App\Service\MovieDB\Model\SpaceModel;
use App\Service\MovieDB\Model\StorageModel;
use App\Service\MovieDB\Model\StorageModelArray;
use JetBrains\PhpStorm\Pure;

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

    public function insertTorrent(TorrentModel $torrent): string
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
        return $space->getPath();
    }

    public function getTorrentFilePath(string $path): ?string
    {
        return (new SpaceModel($path))->getFilePath('torrent.torrent');
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

    #[Pure] private function getSpaceForPath(string $path): SpaceModel {
        return new SpaceModel($path);
    }

    private function insertFile(SpaceModel $space, string $name, $file): bool
    {
        return $space->storeFile($name, $file);
    }

    private function insertValue(SpaceModel $space, array $value, string $type): bool
    {
        return $space->storeValue($type, $value);
    }

    public function insertStorageFile(array $storageFile, string $path)
    {
        $space = $this->getSpaceForPath($path);
        $value = $space->loadValue('files');
        if(!$value) $value = [];
        //$value = [];
        foreach($storageFile as $key=>$file) {
            $value[$key] = $file->toArray();
        }
        $space->storeValue('files', $value);
    }

    public function loadValue(string $path, string $name): ?array
    {
        return (new SpaceModel($path))->loadValue($name);
    }

    public function getDownloadPath(?string $path)
    {
        return (new SpaceModel($path))->getFolder('downloads');
    }
}