<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131018103930 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Comment (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Message ADD comment_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Message ADD CONSTRAINT FK_790009E3F8697D13 FOREIGN KEY (comment_id) REFERENCES Comment (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_790009E3F8697D13 ON Message (comment_id)");
        $this->addSql("ALTER TABLE Pack DROP repost_to_url");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Message DROP FOREIGN KEY FK_790009E3F8697D13");
        $this->addSql("DROP TABLE Comment");
        $this->addSql("DROP INDEX UNIQ_790009E3F8697D13 ON Message");
        $this->addSql("ALTER TABLE Message DROP comment_id");
        $this->addSql("ALTER TABLE Pack ADD repost_to_url VARCHAR(255) DEFAULT NULL");
    }
}
