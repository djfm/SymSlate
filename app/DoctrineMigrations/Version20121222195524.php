<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121222195524 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE PackExport ADD CONSTRAINT FK_32B1769F1919B217 FOREIGN KEY (pack_id) REFERENCES Pack (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_32B1769F1919B217 ON PackExport (pack_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE PackExport DROP FOREIGN KEY FK_32B1769F1919B217");
        $this->addSql("DROP INDEX IDX_32B1769F1919B217 ON PackExport");
    }
}