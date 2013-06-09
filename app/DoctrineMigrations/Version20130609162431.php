<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130609162431 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE PackExport ADD CONSTRAINT FK_32B1769F82F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id)");
        $this->addSql("CREATE INDEX IDX_32B1769F82F1BAF4 ON PackExport (language_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE PackExport DROP FOREIGN KEY FK_32B1769F82F1BAF4");
        $this->addSql("DROP INDEX IDX_32B1769F82F1BAF4 ON PackExport");
    }
}
