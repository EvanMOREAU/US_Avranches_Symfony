<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219131821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_tests ADD palier_id INT DEFAULT NULL, DROP palier');
        $this->addSql('ALTER TABLE tbl_tests ADD CONSTRAINT FK_BC04B64160E28355 FOREIGN KEY (palier_id) REFERENCES tbl_palier (id)');
        $this->addSql('CREATE INDEX IDX_BC04B64160E28355 ON tbl_tests (palier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_tests DROP FOREIGN KEY FK_BC04B64160E28355');
        $this->addSql('DROP INDEX IDX_BC04B64160E28355 ON tbl_tests');
        $this->addSql('ALTER TABLE tbl_tests ADD palier VARCHAR(255) DEFAULT NULL, DROP palier_id');
    }
}
