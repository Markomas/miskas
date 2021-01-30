<?php

namespace App\Command;

use App\Service\Crawler\Crawler;
use App\Service\MovieDB\Indexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

    public function __construct(string $name = null, Crawler $crawler, Indexer $indexer)
    {
        parent::__construct($name);
        $this->crawler = $crawler;
        $this->indexer = $indexer;
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$this->crawler->run();
        $this->indexer->reindex();



        return Command::SUCCESS;
    }
}
