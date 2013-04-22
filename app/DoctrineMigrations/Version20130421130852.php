<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130421130852 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE History (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, language_id INT NOT NULL, translation_id INT NOT NULL, user_id INT NOT NULL, operation VARCHAR(32) NOT NULL, created DATETIME NOT NULL, INDEX IDX_E80749D7537A1329 (message_id), INDEX IDX_E80749D7537A132982F1BAF4 (message_id, language_id), UNIQUE INDEX UNIQ_E80749D782F1BAF4 (language_id), UNIQUE INDEX UNIQ_E80749D79CAA2B25 (translation_id), UNIQUE INDEX UNIQ_E80749D7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE History ADD CONSTRAINT FK_E80749D7537A1329 FOREIGN KEY (message_id) REFERENCES Message (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE History ADD CONSTRAINT FK_E80749D7537A132982F1BAF4 FOREIGN KEY (message_id, language_id) REFERENCES CurrentTranslation (message_id, language_id)");
        $this->addSql("ALTER TABLE History ADD CONSTRAINT FK_E80749D782F1BAF4 FOREIGN KEY (language_id) REFERENCES Language (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE History ADD CONSTRAINT FK_E80749D79CAA2B25 FOREIGN KEY (translation_id) REFERENCES Translation (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE History ADD CONSTRAINT FK_E80749D7A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Translation DROP FOREIGN KEY FK_32F5CAB885D7FB47");
        $this->addSql("DROP INDEX IDX_32F5CAB885D7FB47 ON Translation");
        $this->addSql("ALTER TABLE Translation ADD fuzzyness VARCHAR(4) NOT NULL, DROP reviewed_by, DROP previous_translation_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE History");
        $this->addSql("ALTER TABLE Translation ADD reviewed_by INT DEFAULT NULL, ADD previous_translation_id INT DEFAULT NULL, DROP fuzzyness");
        $this->addSql("ALTER TABLE Translation ADD CONSTRAINT FK_32F5CAB885D7FB47 FOREIGN KEY (reviewed_by) REFERENCES User (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_32F5CAB885D7FB47 ON Translation (reviewed_by)");
    }
}
