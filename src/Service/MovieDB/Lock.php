<?php


namespace App\Service\MovieDB;


use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Lock
{
    private string $rootDirectory;
    private LoggerInterface $logger;
    /**
     * @var false|resource
     */
    private $dbLock;

    public function __construct(string $rootDirectory, LoggerInterface $logger) {
        $this->logger = $logger;
        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR);
        $lockFile = $this->rootDirectory . DIRECTORY_SEPARATOR . 'lock';
        $this->dbLock = fopen($lockFile, "w+");
        $this->logger = $logger;
    }

    public function __destruct() {
        fclose($this->dbLock);
    }

    public function lock() {
        while(!flock($this->dbLock, LOCK_EX | LOCK_NB)) {
            $this->logger->log(LogLevel::INFO, 'MovieDB Lock: lock failed, sleeping');
            sleep(10);
        }
    }

    public function unlock() {
        flock($this->dbLock, LOCK_UN);
    }
}