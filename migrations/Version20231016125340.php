<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016125340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE charts (id INT AUTO_INCREMENT NOT NULL, type LONGTEXT NOT NULL, data LONGTEXT NOT NULL, source_data LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE pdf');
        $this->addSql('ALTER TABLE tbl_user ADD team_id INT DEFAULT NULL, ADD matches_played INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A1296CD8AE FOREIGN KEY (team_id) REFERENCES tbl_team (id)');
        $this->addSql('CREATE INDEX IDX_38B383A1296CD8AE ON tbl_user (team_id)');
        $this->addSql('ALTER TABLE tests ADD date DATETIME DEFAULT NULL, CHANGE cooper cooper VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pdf (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, content LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, stats INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE charts');
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A1296CD8AE');
        $this->addSql('DROP INDEX IDX_38B383A1296CD8AE ON tbl_user');
        $this->addSql('ALTER TABLE tbl_user DROP team_id, DROP matches_played');
        $this->addSql('ALTER TABLE tests DROP date, CHANGE cooper cooper TIME DEFAULT NULL');
    }
}
