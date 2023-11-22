<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231020135029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tests ADD user_id INT NOT NULL, CHANGE vma vma DOUBLE PRECISION DEFAULT NULL, CHANGE cooper cooper VARCHAR(255) NOT NULL, CHANGE conduiteballe conduiteballe TIME DEFAULT NULL, CHANGE vitesse vitesse TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE tests ADD CONSTRAINT FK_1260FC5EA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
        $this->addSql('CREATE INDEX IDX_1260FC5EA76ED395 ON tests (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tests DROP FOREIGN KEY FK_1260FC5EA76ED395');
        $this->addSql('DROP INDEX IDX_1260FC5EA76ED395 ON tests');
        $this->addSql('ALTER TABLE tests DROP user_id, CHANGE vma vma INT DEFAULT NULL, CHANGE cooper cooper INT DEFAULT NULL, CHANGE conduiteballe conduiteballe VARCHAR(255) DEFAULT NULL, CHANGE vitesse vitesse VARCHAR(255) DEFAULT NULL');
    }
}
