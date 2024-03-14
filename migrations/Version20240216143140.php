<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216143140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user ADD palier_numero INT DEFAULT NULL, DROP palier');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A160E28355 FOREIGN KEY (palier_numero) REFERENCES tbl_palier (numero)');
        $this->addSql('CREATE INDEX IDX_38B383A160E28355 ON tbl_user (palier_numero)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A160E28355');
        $this->addSql('DROP INDEX IDX_38B383A160E28355 ON tbl_user');
        $this->addSql('ALTER TABLE tbl_user ADD palier VARCHAR(255) DEFAULT NULL, DROP palier_numero');
    }
}
