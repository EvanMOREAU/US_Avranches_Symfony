<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005070358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_player (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birthdate DATE NOT NULL, matches_played INT NOT NULL, INDEX IDX_15BF13DA296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, matches_played INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_38B383A1F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test_entity (id INT AUTO_INCREMENT NOT NULL, vma INT DEFAULT NULL, cooper TIME DEFAULT NULL, jongle_gauche INT DEFAULT NULL, jongle_droit INT DEFAULT NULL, jongle_tete INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_player ADD CONSTRAINT FK_15BF13DA296CD8AE FOREIGN KEY (team_id) REFERENCES tbl_team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_player DROP FOREIGN KEY FK_15BF13DA296CD8AE');
        $this->addSql('DROP TABLE tbl_player');
        $this->addSql('DROP TABLE tbl_team');
        $this->addSql('DROP TABLE tbl_user');
        $this->addSql('DROP TABLE test_entity');
    }
}
