<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130421132752 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Translation DROP FOREIGN KEY FK_32F5CAB86EEF4BE2");
        $this->addSql("DROP TABLE TranslationSubmission");
        $this->addSql("DROP INDEX UNIQ_32F5CAB86EEF4BE2 ON Translation");
        $this->addSql("ALTER TABLE Translation DROP translation_submission_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE TranslationSubmission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_ACE7B56DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE TranslationSubmission ADD CONSTRAINT FK_ACE7B56DA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Translation ADD translation_submission_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Translation ADD CONSTRAINT FK_32F5CAB86EEF4BE2 FOREIGN KEY (translation_submission_id) REFERENCES TranslationSubmission (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_32F5CAB86EEF4BE2 ON Translation (translation_submission_id)");
    }
}
