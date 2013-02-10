<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130210174109 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Translation collate utf8_bin");
        $this->addSql("ALTER TABLE Message     collate utf8_bin");

    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE Translation collate utf8_general_ci");
        $this->addSql("ALTER TABLE Message     collate utf8_general_ci");
    }
}
