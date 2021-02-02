<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210131071154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, imdb VARCHAR(32) DEFAULT NULL, tmdb INT DEFAULT NULL, season INT DEFAULT NULL, episode INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE storage_file ADD movie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE storage_file ADD CONSTRAINT FK_85B2FD058F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('CREATE INDEX IDX_85B2FD058F93B6FC ON storage_file (movie_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage_file DROP FOREIGN KEY FK_85B2FD058F93B6FC');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP INDEX IDX_85B2FD058F93B6FC ON storage_file');
        $this->addSql('ALTER TABLE storage_file DROP movie_id');
    }
}
