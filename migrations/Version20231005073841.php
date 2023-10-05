<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005073841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user ADD team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A1296CD8AE FOREIGN KEY (team_id) REFERENCES tbl_team (id)');
        $this->addSql('CREATE INDEX IDX_38B383A1296CD8AE ON tbl_user (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A1296CD8AE');
        $this->addSql('DROP INDEX IDX_38B383A1296CD8AE ON tbl_user');
        $this->addSql('ALTER TABLE tbl_user DROP team_id');
    }
}
