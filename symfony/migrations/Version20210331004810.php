<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331004810 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, iso VARCHAR(3) NOT NULL, description VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fixer_log (id INT AUTO_INCREMENT NOT NULL, from_currency_id INT NOT NULL, to_currency_id INT NOT NULL, to_amount NUMERIC(10, 2) NOT NULL, from_amount NUMERIC(10, 2) NOT NULL, date DATE NOT NULL, INDEX IDX_E1684A39A66BB013 (from_currency_id), INDEX IDX_E1684A3916B7BF15 (to_currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fixer_log ADD CONSTRAINT FK_E1684A39A66BB013 FOREIGN KEY (from_currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE fixer_log ADD CONSTRAINT FK_E1684A3916B7BF15 FOREIGN KEY (to_currency_id) REFERENCES currency (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fixer_log DROP FOREIGN KEY FK_E1684A39A66BB013');
        $this->addSql('ALTER TABLE fixer_log DROP FOREIGN KEY FK_E1684A3916B7BF15');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE fixer_log');
    }
}
