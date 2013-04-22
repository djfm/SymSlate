<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130421144559 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE History DROP INDEX UNIQ_E80749D79CAA2B25, ADD INDEX IDX_E80749D79CAA2B25 (translation_id)");
        $this->addSql("ALTER TABLE History DROP INDEX UNIQ_E80749D7A76ED395, ADD INDEX IDX_E80749D7A76ED395 (user_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE History DROP INDEX IDX_E80749D79CAA2B25, ADD UNIQUE INDEX UNIQ_E80749D79CAA2B25 (translation_id)");
        $this->addSql("ALTER TABLE History DROP INDEX IDX_E80749D7A76ED395, ADD UNIQUE INDEX UNIQ_E80749D7A76ED395 (user_id)");
    }
}
