<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130604171622 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE IgnoredSection (id INT AUTO_INCREMENT NOT NULL, pack_id INT NOT NULL, category VARCHAR(255) NOT NULL, section VARCHAR(255) NOT NULL, language_id INT NOT NULL, INDEX IDX_80D0D39A1919B217 (pack_id), UNIQUE INDEX pack_id_category_section_language_id_idx (pack_id, category, section, language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE IgnoredSection ADD CONSTRAINT FK_80D0D39A1919B217 FOREIGN KEY (pack_id) REFERENCES Pack (id) ON DELETE CASCADE");
        $this->addSql("DROP INDEX message_txt_idx ON Message");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE IgnoredSection");
        $this->addSql("CREATE INDEX message_txt_idx ON Message (text)");
    }
}
