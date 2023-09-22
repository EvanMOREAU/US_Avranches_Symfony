<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921072023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_players ADD team_matches_played_id INT NOT NULL, ADD player_matches_played INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_players ADD CONSTRAINT FK_7D1D4BD2CE9C90F8 FOREIGN KEY (team_matches_played_id) REFERENCES tbl_teams (id)');
        $this->addSql('CREATE INDEX IDX_7D1D4BD2CE9C90F8 ON tbl_players (team_matches_played_id)');
        $this->addSql('ALTER TABLE tbl_teams ADD matches_played INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_players DROP FOREIGN KEY FK_7D1D4BD2CE9C90F8');
        $this->addSql('DROP INDEX IDX_7D1D4BD2CE9C90F8 ON tbl_players');
        $this->addSql('ALTER TABLE tbl_players DROP team_matches_played_id, DROP player_matches_played');
        $this->addSql('ALTER TABLE tbl_teams DROP matches_played');
    }
}
