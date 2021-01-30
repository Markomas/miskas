<?php


namespace App\Service\Crawler;


use App\Service\Crawler\Model\TorrentModelArray;

interface SiteInterface
{
    public function setConfig(array $config): bool;
    public function login(): bool;
    public function scrap(int $page = 0): ?TorrentModelArray;
    public function logout(): bool;
}