<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130113181106 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Translation DROP FOREIGN KEY FK_32F5CAB86EEF4BE2");
        $this->addSql("ALTER TABLE Translation ADD CONSTRAINT FK_32F5CAB86EEF4BE2 FOREIGN KEY (translation_submission_id) REFERENCES TranslationSubmission (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Translation DROP FOREIGN KEY FK_32F5CAB86EEF4BE2");
        $this->addSql("ALTER TABLE Translation ADD CONSTRAINT FK_32F5CAB86EEF4BE2 FOREIGN KEY (translation_submission_id) REFERENCES TranslationSubmission (id)");
    }
}
