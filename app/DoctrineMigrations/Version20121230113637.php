<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121230113637 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE CurrentTranslation DROP FOREIGN KEY FK_D3434B826EEF4BE2");
        $this->addSql("ALTER TABLE CurrentTranslation DROP FOREIGN KEY FK_D3434B822A86559F");
        $this->addSql("DROP INDEX classification_id_language_id_idx ON CurrentTranslation");
        $this->addSql("DROP INDEX IDX_D3434B822A86559F ON CurrentTranslation");
        $this->addSql("DROP INDEX IDX_D3434B826EEF4BE2 ON CurrentTranslation");
        $this->addSql("ALTER TABLE CurrentTranslation CHANGE translation_submission_id message_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B82537A1329 FOREIGN KEY (message_id) REFERENCES Message (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_D3434B82537A1329 ON CurrentTranslation (message_id)");
        $this->addSql("CREATE UNIQUE INDEX fm_message_id_language_id_idx ON CurrentTranslation (message_id, language_id)");
        $this->addSql("ALTER TABLE Translation ADD translation_submission_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Translation ADD CONSTRAINT FK_32F5CAB86EEF4BE2 FOREIGN KEY (translation_submission_id) REFERENCES TranslationSubmission (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_32F5CAB86EEF4BE2 ON Translation (translation_submission_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE CurrentTranslation DROP FOREIGN KEY FK_D3434B82537A1329");
        $this->addSql("DROP INDEX IDX_D3434B82537A1329 ON CurrentTranslation");
        $this->addSql("DROP INDEX fm_message_id_language_id_idx ON CurrentTranslation");
        $this->addSql("ALTER TABLE CurrentTranslation CHANGE message_id translation_submission_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B826EEF4BE2 FOREIGN KEY (translation_submission_id) REFERENCES TranslationSubmission (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B822A86559F FOREIGN KEY (classification_id) REFERENCES Classification (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX classification_id_language_id_idx ON CurrentTranslation (classification_id, language_id)");
        $this->addSql("CREATE INDEX IDX_D3434B822A86559F ON CurrentTranslation (classification_id)");
        $this->addSql("CREATE INDEX IDX_D3434B826EEF4BE2 ON CurrentTranslation (translation_submission_id)");
        $this->addSql("ALTER TABLE Translation DROP FOREIGN KEY FK_32F5CAB86EEF4BE2");
        $this->addSql("DROP INDEX UNIQ_32F5CAB86EEF4BE2 ON Translation");
        $this->addSql("ALTER TABLE Translation DROP translation_submission_id");
    }
}
