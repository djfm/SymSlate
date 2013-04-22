<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130421144214 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE History DROP FOREIGN KEY FK_E80749D7537A132982F1BAF4");
        $this->addSql("DROP INDEX IDX_E80749D7537A132982F1BAF4 ON History");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE History ADD CONSTRAINT FK_E80749D7537A132982F1BAF4 FOREIGN KEY (message_id, language_id) REFERENCES CurrentTranslation (message_id, language_id)");
        $this->addSql("CREATE INDEX IDX_E80749D7537A132982F1BAF4 ON History (message_id, language_id)");
    }
}
