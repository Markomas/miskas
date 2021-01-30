<?php


namespace App\Service\Crawler\Site;


use App\Service\Crawler\Model\TorrentModel;
use App\Service\Crawler\Model\TorrentModelArray;
use App\Service\Crawler\SiteInterface;
use Goutte\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;

class TorrentaiSite implements SiteInterface
{

    private Client $client;
    private string $clientId;
    private string $urlBase;
    private string $urlList;
    private string $urlLogin;
    private LoggerInterface $logger;

    public function __construct(Client $client, LoggerInterface $logger) {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function setConfig(array $config): bool
    {
        if(!isset($config['cookie'])) return false;
        $this->clientId = 'torrentai';
        $hostname = 'torrent.lt';
        $this->urlBase = 'https://torrent.lt/lt/';
        $this->urlLogin = $this->urlBase . 'main';
        $this->urlList = $this->urlBase . 'torrents?search=&cats[]=76&cats[]=33&cats[]=43&cats[]=74&cats[]=58&cats[]=69&page={{page}}';

        $cookie = explode(': ', $config['cookie']);

        $this->client->getCookieJar()->set(new Cookie($cookie[0], $cookie[1], null, null, $hostname));

        return true;
    }

    public function login(): bool {
        $crawler = $this->client->request('GET', $this->urlLogin);
        $html = $crawler->html();
        if(!str_contains($html, 'Santykis')) {
            $this->logger->log(LogLevel::WARNING, 'Torrentai login failed');
            return false;
        }
        return true;
    }

    public function scrap(int $page = 0):  ?TorrentModelArray
    {
        $out = new TorrentModelArray();
        $crawler = $this->client->request('GET', str_replace('{{page}}', $page, $this->urlList));
        $torrentPages = $crawler
            ->filter('table')
            ->filter('tr')
            ->filter('a')
            ->each(
                function (Crawler $row) {
                    return $url[] = $row->attr('href');
                }
            );
        foreach ($torrentPages as $link) {
            if (strpos($link, 'details?') !== 0) {
                continue;
            }

            if (!preg_match('/details\?(\d+)/', $link, $matches)) {
                continue;
            }

            $id = intval(str_replace('details?', '', $matches[0]));

            if(isset($out[$this->clientId . '_' . $id])) {
                continue;
            }

            $this->logger->log(LogLevel::INFO, 'Torrentai: parsing details of: ' . $id);

            $torrent = $this->scrapTorrentPage($link);
            if (!$torrent) {
                $this->logger->log(LogLevel::INFO, 'Torrentai: parsing details of: ' . $id . ' empty');
                continue;
            }
            $torrent->setId($id);
            $torrent->setClient($this->clientId);
            $out[$this->clientId . '_' . $id] = $torrent;
        }

        return $out;
    }

    public function logout(): bool
    {
        return true;
    }

    private function scrapTorrentPage($uri): ?TorrentModel
    {
        $crawl = $this->client->request('GET', $this->urlBase . $uri);
        //if(!preg_match('/download\?id=(\d+)/', $crawl->html(), $matches)) {
        if (!preg_match('/t(\d+)/', $uri, $matches)) {
            return null;
        }
        $downloadUri = 'download?id=' . intval(str_replace('t', '', $matches[0]));
        $file = $this->downloadTorrent($downloadUri);
        if (!preg_match_all("/tt\\d{7,8}/", $crawl->html(), $matches)) {
            return null;
        }

        $imdbIds = $matches[0];
        if (count(array_flip($imdbIds)) > 1 || !isset($imdbIds[0])) {
            return null;
        }
        $imdbId = $imdbIds[0];


        if(!preg_match('/<title>(.*)<\/title>/', str_replace(array("\r","\n"),"",$crawl->html()), $matches)) {
            return null;
        }
        $title = trim($matches[1]);

        $torrent = new TorrentModel();
        $torrent->setTitle($title);
        $torrent->setFile($file);
        $torrent->setHtml($crawl->html());
        $torrent->setImdb($imdbId);
        return $torrent;
    }

    private function downloadTorrent($uri)
    {
        $this->client->request('GET', $this->urlBase . $uri);
        return $this->client->getResponse()->getContent();
    }
}