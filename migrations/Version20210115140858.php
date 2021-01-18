<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115140858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bet (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, competition_id INT NOT NULL, run_id INT DEFAULT NULL, team_id INT DEFAULT NULL, team_member_id INT DEFAULT NULL, bet_category_id INT NOT NULL, designation VARCHAR(255) NOT NULL, amount INT NOT NULL, odds INT NOT NULL, is_winning TINYINT(1) DEFAULT NULL, INDEX IDX_FBF0EC9BA76ED395 (user_id), UNIQUE INDEX UNIQ_FBF0EC9B7B39D312 (competition_id), UNIQUE INDEX UNIQ_FBF0EC9B84E3FEC4 (run_id), UNIQUE INDEX UNIQ_FBF0EC9B296CD8AE (team_id), UNIQUE INDEX UNIQ_FBF0EC9BC292CD19 (team_member_id), INDEX IDX_FBF0EC9B7876E9B0 (bet_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bet_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE billing (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, designation VARCHAR(255) NOT NULL, order_number INT NOT NULL, invoice_number INT NOT NULL, amount INT NOT NULL, commission_rate INT NOT NULL, issue_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivery_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_EC224CAAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, sport_id INT NOT NULL, result_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', country VARCHAR(255) NOT NULL, min_runs INT NOT NULL, max_runs INT DEFAULT NULL, UNIQUE INDEX UNIQ_B50A2CB1AC78BCF8 (sport_id), UNIQUE INDEX UNIQ_B50A2CB17A7B643 (result_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_bet_category (competition_id INT NOT NULL, bet_category_id INT NOT NULL, INDEX IDX_188D9EFB7B39D312 (competition_id), INDEX IDX_188D9EFB7876E9B0 (bet_category_id), PRIMARY KEY(competition_id, bet_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, code VARCHAR(20) NOT NULL, date_format VARCHAR(50) NOT NULL, time_format VARCHAR(50) NOT NULL, capital_time_zone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, place VARCHAR(255) NOT NULL, time_zone VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `member` (id INT AUTO_INCREMENT NOT NULL, member_role_id INT NOT NULL, member_status_id INT NOT NULL, team_id INT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_70E4FA7869F79538 (member_role_id), INDEX IDX_70E4FA782BDFD678 (member_status_id), INDEX IDX_70E4FA78296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, bet_category_id INT NOT NULL, team_id INT DEFAULT NULL, team_member_id INT DEFAULT NULL, competition_id INT NOT NULL, run_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, value INT NOT NULL, winner TINYINT(1) NOT NULL, INDEX IDX_136AC1137876E9B0 (bet_category_id), INDEX IDX_136AC113296CD8AE (team_id), INDEX IDX_136AC113C292CD19 (team_member_id), INDEX IDX_136AC1137B39D312 (competition_id), INDEX IDX_136AC11384E3FEC4 (run_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE run (id INT AUTO_INCREMENT NOT NULL, competition_id INT NOT NULL, location_id INT NOT NULL, name VARCHAR(255) NOT NULL, event VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', no_winner TINYINT(1) DEFAULT NULL, INDEX IDX_5076A4C07B39D312 (competition_id), INDEX IDX_5076A4C064D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE run_team (run_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_581EF93B84E3FEC4 (run_id), INDEX IDX_581EF93B296CD8AE (team_id), PRIMARY KEY(run_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE run_result (run_id INT NOT NULL, result_id INT NOT NULL, INDEX IDX_1956F02E84E3FEC4 (run_id), INDEX IDX_1956F02E7A7B643 (result_id), PRIMARY KEY(run_id, result_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, run_type VARCHAR(255) NOT NULL, individual_type TINYINT(1) NOT NULL, collective_type TINYINT(1) NOT NULL, min_teams_by_run INT NOT NULL, max_teams_by_run INT DEFAULT NULL, min_members_by_team INT NOT NULL, max_members_by_team INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, sport_id INT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_C4E0A61FAC78BCF8 (sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, wallet_id INT NOT NULL, language_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, civility VARCHAR(60) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, billing_address VARCHAR(255) DEFAULT NULL, billing_city VARCHAR(255) DEFAULT NULL, billing_postcode VARCHAR(255) DEFAULT NULL, billing_country VARCHAR(255) DEFAULT NULL, birth_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', time_zone_selected VARCHAR(255) NOT NULL, deleted_status TINYINT(1) NOT NULL, deleted_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', suspended_status TINYINT(1) NOT NULL, suspended_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', activated_status TINYINT(1) NOT NULL, activated_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_verified TINYINT(1) NOT NULL, newsletters TINYINT(1) NOT NULL, identity_document VARCHAR(255) NOT NULL, residence_proof VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649712520F3 (wallet_id), UNIQUE INDEX UNIQ_8D93D64982F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B84E3FEC4 FOREIGN KEY (run_id) REFERENCES run (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9BC292CD19 FOREIGN KEY (team_member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B7876E9B0 FOREIGN KEY (bet_category_id) REFERENCES bet_category (id)');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB17A7B643 FOREIGN KEY (result_id) REFERENCES result (id)');
        $this->addSql('ALTER TABLE competition_bet_category ADD CONSTRAINT FK_188D9EFB7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_bet_category ADD CONSTRAINT FK_188D9EFB7876E9B0 FOREIGN KEY (bet_category_id) REFERENCES bet_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA7869F79538 FOREIGN KEY (member_role_id) REFERENCES member_role (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA782BDFD678 FOREIGN KEY (member_status_id) REFERENCES member_status (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC1137876E9B0 FOREIGN KEY (bet_category_id) REFERENCES bet_category (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113C292CD19 FOREIGN KEY (team_member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC1137B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC11384E3FEC4 FOREIGN KEY (run_id) REFERENCES run (id)');
        $this->addSql('ALTER TABLE run ADD CONSTRAINT FK_5076A4C07B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE run ADD CONSTRAINT FK_5076A4C064D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE run_team ADD CONSTRAINT FK_581EF93B84E3FEC4 FOREIGN KEY (run_id) REFERENCES run (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE run_team ADD CONSTRAINT FK_581EF93B296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE run_result ADD CONSTRAINT FK_1956F02E84E3FEC4 FOREIGN KEY (run_id) REFERENCES run (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE run_result ADD CONSTRAINT FK_1956F02E7A7B643 FOREIGN KEY (result_id) REFERENCES result (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FAC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64982F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9B7876E9B0');
        $this->addSql('ALTER TABLE competition_bet_category DROP FOREIGN KEY FK_188D9EFB7876E9B0');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC1137876E9B0');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9B7B39D312');
        $this->addSql('ALTER TABLE competition_bet_category DROP FOREIGN KEY FK_188D9EFB7B39D312');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC1137B39D312');
        $this->addSql('ALTER TABLE run DROP FOREIGN KEY FK_5076A4C07B39D312');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64982F1BAF4');
        $this->addSql('ALTER TABLE run DROP FOREIGN KEY FK_5076A4C064D218E');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9BC292CD19');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113C292CD19');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA7869F79538');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA782BDFD678');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB17A7B643');
        $this->addSql('ALTER TABLE run_result DROP FOREIGN KEY FK_1956F02E7A7B643');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9B84E3FEC4');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC11384E3FEC4');
        $this->addSql('ALTER TABLE run_team DROP FOREIGN KEY FK_581EF93B84E3FEC4');
        $this->addSql('ALTER TABLE run_result DROP FOREIGN KEY FK_1956F02E84E3FEC4');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1AC78BCF8');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FAC78BCF8');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9B296CD8AE');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78296CD8AE');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113296CD8AE');
        $this->addSql('ALTER TABLE run_team DROP FOREIGN KEY FK_581EF93B296CD8AE');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9BA76ED395');
        $this->addSql('ALTER TABLE billing DROP FOREIGN KEY FK_EC224CAAA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649712520F3');
        $this->addSql('DROP TABLE bet');
        $this->addSql('DROP TABLE bet_category');
        $this->addSql('DROP TABLE billing');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_bet_category');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE `member`');
        $this->addSql('DROP TABLE member_role');
        $this->addSql('DROP TABLE member_status');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE run');
        $this->addSql('DROP TABLE run_team');
        $this->addSql('DROP TABLE run_result');
        $this->addSql('DROP TABLE sport');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wallet');
    }
}
