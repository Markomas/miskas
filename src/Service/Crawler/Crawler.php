<?php


namespace App\Service\Crawler;


use App\Service\Crawler\Model\TorrentModelArray;
use App\Service\MovieDB\MovieDB;

class Crawler
{
    private array $sites;
    private array $config;
    /**
     * @var MovieDB
     */
    private MovieDB $movieDB;

    public function __construct(array $sites, array $config, MovieDB $movieDB)
    {
        $this->sites = $sites;
        $this->config = $config;
        $this->movieDB = $movieDB;
    }

    public function run(): void
    {
        foreach ($this->sites as $site) {
            if(!isset($this->config[get_class($site)])) {
                continue;
            }
            $config = $this->config[get_class($site)];
            $page = 0;

            if(!$site->setConfig($config)) continue;
            //if(!$site->login()) continue;
            //if(!$torrents = $site->scrap($page)) continue;
            //file_put_contents('test.dat', serialize($torrents));
            $torrents = unserialize(file_get_contents('test.dat'));
            $this->persist($torrents);
            return;
            if(!$site->logout()) continue;
        }
    }

    private function persist(?TorrentModelArray $torrents)
    {
        foreach($torrents as $torrent) {
            $this->movieDB->saveTorrent($torrent);
        }
    }
}