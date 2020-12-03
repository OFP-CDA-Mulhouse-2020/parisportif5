<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203134732 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE positive');
        $this->addSql('ALTER TABLE user ADD email_address VARCHAR(255) NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD time_zone_selected VARCHAR(255) NOT NULL, ADD deleted_status TINYINT(1) NOT NULL, ADD deleted_date DATETIME DEFAULT NULL, ADD suspended_status TINYINT(1) NOT NULL, ADD suspended_date DATETIME DEFAULT NULL, ADD activated_status TINYINT(1) NOT NULL, ADD activated_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE positive (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user DROP email_address, DROP password, DROP time_zone_selected, DROP deleted_status, DROP deleted_date, DROP suspended_status, DROP suspended_date, DROP activated_status, DROP activated_date');
    }
}
