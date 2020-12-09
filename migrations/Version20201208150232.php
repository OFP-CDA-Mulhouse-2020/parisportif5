<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201208150232 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, civility VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, billing_address VARCHAR(255) DEFAULT NULL, billing_city VARCHAR(255) DEFAULT NULL, billing_postcode VARCHAR(255) DEFAULT NULL, billing_country VARCHAR(255) DEFAULT NULL, birth_date DATE NOT NULL, time_zone_selected VARCHAR(255) DEFAULT NULL, deleted_status TINYINT(1) NOT NULL, deleted_date DATETIME DEFAULT NULL, suspended_status TINYINT(1) NOT NULL, suspended_date DATETIME DEFAULT NULL, activated_status TINYINT(1) NOT NULL, activated_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
