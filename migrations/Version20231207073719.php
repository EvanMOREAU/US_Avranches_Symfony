<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207073719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_weight DROP FOREIGN KEY FK_8A6B3CFE9D86650F');
        $this->addSql('DROP INDEX IDX_8A6B3CFE9D86650F ON tbl_weight');
        $this->addSql('ALTER TABLE tbl_weight CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_weight ADD CONSTRAINT FK_8A6B3CFEA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
        $this->addSql('CREATE INDEX IDX_8A6B3CFEA76ED395 ON tbl_weight (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_weight DROP FOREIGN KEY FK_8A6B3CFEA76ED395');
        $this->addSql('DROP INDEX IDX_8A6B3CFEA76ED395 ON tbl_weight');
        $this->addSql('ALTER TABLE tbl_weight CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE tbl_weight ADD CONSTRAINT FK_8A6B3CFE9D86650F FOREIGN KEY (user_id_id) REFERENCES tbl_user (id)');
        $this->addSql('CREATE INDEX IDX_8A6B3CFE9D86650F ON tbl_weight (user_id_id)');
    }
}
