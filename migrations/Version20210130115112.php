<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210130115112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage_file ADD storage_id INT DEFAULT NULL, ADD is_skipped TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE storage_file ADD CONSTRAINT FK_85B2FD055CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage_space (id)');
        $this->addSql('CREATE INDEX IDX_85B2FD055CC5DB90 ON storage_file (storage_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE storage_file DROP FOREIGN KEY FK_85B2FD055CC5DB90');
        $this->addSql('DROP INDEX IDX_85B2FD055CC5DB90 ON storage_file');
        $this->addSql('ALTER TABLE storage_file DROP storage_id, DROP is_skipped');
    }
}
