<?php

namespace App\Command;

use App\Service\Crawler\Crawler;
use App\Service\FindDublicates\Deduplicator;
use App\Service\MovieDB\Indexer;
use App\Service\Torrent\Downloader;
use App\Service\Torrent\Scanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    protected static $defaultName = 'app:test';
    /**
     * @var Crawler
     */
    private $crawler;
    /**
     * @var Indexer
     */
    private Indexer $indexer;
    /**
     * @var Scanner
     */
    private Scanner $scanner;
    /**
     * @var Deduplicator
     */
    private Deduplicator $deduplicator;
    /**
     * @var Downloader
     */
    private Downloader $downloader;

    public function __construct(string $name = null, Crawler $crawler, Indexer $indexer, Scanner $scanner, Deduplicator $deduplicator, Downloader $downloader)
    {
        parent::__construct($name);
        $this->crawler = $crawler;
        $this->indexer = $indexer;
        $this->scanner = $scanner;
        $this->deduplicator = $deduplicator;
        $this->downloader = $downloader;
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$this->crawler->run();
        //$this->indexer->reindex();
        //while($this->scanner->scan()) {};
        //$this->scanner->scan();
        //$this->deduplicator->scan();
        $this->downloader->run();

        return Command::SUCCESS;
    }
}
