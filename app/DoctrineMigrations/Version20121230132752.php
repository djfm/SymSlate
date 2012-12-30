<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121230132752 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B8282F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_D3434B8282F1BAF4 ON CurrentTranslation (language_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE CurrentTranslation DROP FOREIGN KEY FK_D3434B8282F1BAF4");
        $this->addSql("DROP INDEX IDX_D3434B8282F1BAF4 ON CurrentTranslation");
    }
}
