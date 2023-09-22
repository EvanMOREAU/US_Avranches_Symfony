<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921065119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_players (id INT AUTO_INCREMENT NOT NULL, team_id_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birthyear INT NOT NULL, INDEX IDX_7D1D4BD2B842D717 (team_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_teams (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_players ADD CONSTRAINT FK_7D1D4BD2B842D717 FOREIGN KEY (team_id_id) REFERENCES tbl_teams (id)');
        $this->addSql('ALTER TABLE tbl_joueurs DROP FOREIGN KEY FK_ABAE80E96D861B89');
        $this->addSql('DROP TABLE tbl_equipes');
        $this->addSql('DROP TABLE tbl_joueurs');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_equipes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tbl_joueurs (id INT AUTO_INCREMENT NOT NULL, equipe_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, annee_naissance INT NOT NULL, INDEX IDX_ABAE80E96D861B89 (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tbl_joueurs ADD CONSTRAINT FK_ABAE80E96D861B89 FOREIGN KEY (equipe_id) REFERENCES tbl_equipes (id)');
        $this->addSql('ALTER TABLE tbl_players DROP FOREIGN KEY FK_7D1D4BD2B842D717');
        $this->addSql('DROP TABLE tbl_players');
        $this->addSql('DROP TABLE tbl_teams');
    }
}
