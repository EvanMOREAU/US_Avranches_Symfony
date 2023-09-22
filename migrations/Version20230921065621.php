<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921065621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_players DROP FOREIGN KEY FK_7D1D4BD2B842D717');
        $this->addSql('DROP INDEX IDX_7D1D4BD2B842D717 ON tbl_players');
        $this->addSql('ALTER TABLE tbl_players CHANGE team_id_id team_id INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_players ADD CONSTRAINT FK_7D1D4BD2296CD8AE FOREIGN KEY (team_id) REFERENCES tbl_teams (id)');
        $this->addSql('CREATE INDEX IDX_7D1D4BD2296CD8AE ON tbl_players (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_players DROP FOREIGN KEY FK_7D1D4BD2296CD8AE');
        $this->addSql('DROP INDEX IDX_7D1D4BD2296CD8AE ON tbl_players');
        $this->addSql('ALTER TABLE tbl_players CHANGE team_id team_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_players ADD CONSTRAINT FK_7D1D4BD2B842D717 FOREIGN KEY (team_id_id) REFERENCES tbl_teams (id)');
        $this->addSql('CREATE INDEX IDX_7D1D4BD2B842D717 ON tbl_players (team_id_id)');
    }
}
