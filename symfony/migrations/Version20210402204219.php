<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210402204219 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883F61587F41 ON currency (iso)');
        $this->addSql('ALTER TABLE fixer_log CHANGE to_amount to_amount NUMERIC(10, 0) NOT NULL, CHANGE rate rate NUMERIC(10, 0) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_6956883F61587F41 ON currency');
        $this->addSql('ALTER TABLE fixer_log CHANGE to_amount to_amount NUMERIC(10, 2) NOT NULL, CHANGE rate rate NUMERIC(14, 8) NOT NULL');
    }
}
