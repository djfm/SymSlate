<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121222152727 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE session (session_id VARCHAR(255) NOT NULL, session_value LONGTEXT NOT NULL, session_time INT NOT NULL, PRIMARY KEY(session_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE CurrentTranslation ADD translation_submission_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B826EEF4BE2 FOREIGN KEY (translation_submission_id) REFERENCES TranslationSubmission (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_D3434B826EEF4BE2 ON CurrentTranslation (translation_submission_id)");
        $this->addSql("ALTER TABLE TranslationSubmission DROP FOREIGN KEY FK_ACE7B56D9CAA2B25");
        $this->addSql("ALTER TABLE TranslationSubmission DROP FOREIGN KEY FK_ACE7B56D2A86559F");
        $this->addSql("DROP INDEX UNIQ_ACE7B56D9CAA2B25 ON TranslationSubmission");
        $this->addSql("DROP INDEX IDX_ACE7B56D2A86559F ON TranslationSubmission");
        $this->addSql("ALTER TABLE TranslationSubmission DROP translation_id, DROP classification_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("DROP TABLE session");
        $this->addSql("ALTER TABLE CurrentTranslation DROP FOREIGN KEY FK_D3434B826EEF4BE2");
        $this->addSql("DROP INDEX IDX_D3434B826EEF4BE2 ON CurrentTranslation");
        $this->addSql("ALTER TABLE CurrentTranslation DROP translation_submission_id");
        $this->addSql("ALTER TABLE TranslationSubmission ADD translation_id INT NOT NULL, ADD classification_id INT NOT NULL");
        $this->addSql("ALTER TABLE TranslationSubmission ADD CONSTRAINT FK_ACE7B56D9CAA2B25 FOREIGN KEY (translation_id) REFERENCES Translation (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE TranslationSubmission ADD CONSTRAINT FK_ACE7B56D2A86559F FOREIGN KEY (classification_id) REFERENCES Classification (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_ACE7B56D9CAA2B25 ON TranslationSubmission (translation_id)");
        $this->addSql("CREATE INDEX IDX_ACE7B56D2A86559F ON TranslationSubmission (classification_id)");
    }
}
