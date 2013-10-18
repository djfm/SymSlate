<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131018121213 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Message DROP FOREIGN KEY FK_790009E3F8697D13");
        $this->addSql("ALTER TABLE Message ADD CONSTRAINT FK_790009E3F8697D13 FOREIGN KEY (comment_id) REFERENCES Comment (id) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Message DROP FOREIGN KEY FK_790009E3F8697D13");
        $this->addSql("ALTER TABLE Message ADD CONSTRAINT FK_790009E3F8697D13 FOREIGN KEY (comment_id) REFERENCES Comment (id)");
    }
}
