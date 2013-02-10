<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130210182925 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('alter table Message     convert to character set utf8 collate utf8_bin');
        $this->addSql('alter table Translation convert to character set utf8 collate utf8_bin');
    }

    public function down(Schema $schema)
    {
        $this->addSql('alter table Message     convert to character set utf8 collate utf8_general_ci');
        $this->addSql('alter table Translation convert to character set utf8 collate utf8_general_ci');
    }
}
