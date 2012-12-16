<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121213225655 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE CurrentTranslation (id INT AUTO_INCREMENT NOT NULL, classification_id INT NOT NULL, translation_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_D3434B822A86559F (classification_id), INDEX IDX_D3434B829CAA2B25 (translation_id), UNIQUE INDEX classification_id_language_id_idx (classification_id, language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B822A86559F FOREIGN KEY (classification_id) REFERENCES Classification (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE CurrentTranslation ADD CONSTRAINT FK_D3434B829CAA2B25 FOREIGN KEY (translation_id) REFERENCES Translation (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE classification_translation");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE classification_translation (classification_id INT NOT NULL, translation_id INT NOT NULL, INDEX IDX_8F69654F2A86559F (classification_id), INDEX IDX_8F69654F9CAA2B25 (translation_id), PRIMARY KEY(classification_id, translation_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE classification_translation ADD CONSTRAINT FK_8F69654F9CAA2B25 FOREIGN KEY (translation_id) REFERENCES Translation (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE classification_translation ADD CONSTRAINT FK_8F69654F2A86559F FOREIGN KEY (classification_id) REFERENCES Classification (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE CurrentTranslation");
    }
}