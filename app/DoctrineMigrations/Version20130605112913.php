<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130605112913 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
	$this->addSql('CREATE INDEX message_text_idx ON Message (text (256))');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
	$this->addSql('DROP INDEX Message.message_text_idx');
    }
}
