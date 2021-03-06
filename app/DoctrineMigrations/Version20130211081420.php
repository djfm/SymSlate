<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130211081420 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Message DROP FOREIGN KEY FK_790009E3B868F34B");
        $this->addSql("DROP INDEX IDX_790009E3B868F34B ON Message");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Message ADD CONSTRAINT FK_790009E3B868F34B FOREIGN KEY (messages_import_id) REFERENCES MessagesImport (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_790009E3B868F34B ON Message (messages_import_id)");
    }
}
