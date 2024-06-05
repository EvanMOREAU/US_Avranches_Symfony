<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605140543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_attendance (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, gathering_id INT NOT NULL, is_present TINYINT(1) NOT NULL, reason VARCHAR(255) DEFAULT NULL, INDEX IDX_4A04700A76ED395 (user_id), INDEX IDX_4A047007BA827B5 (gathering_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_chart_configuration (id INT AUTO_INCREMENT NOT NULL, chart_type VARCHAR(255) NOT NULL, config_data JSON NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_equipe (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_A9EFD3AA12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_gathering (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, made_by_id INT NOT NULL, gathering_date DATETIME NOT NULL, type VARCHAR(255) NOT NULL, gathering_happened_date DATETIME NOT NULL, INDEX IDX_84089C2D12469DE2 (category_id), INDEX IDX_84089C2D90B9D269 (made_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_height (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, date DATE NOT NULL, INDEX IDX_78EB8CB0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_palier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, objectif VARCHAR(255) DEFAULT NULL, numero INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_player_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(4) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_411E70CEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_tests (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, palier_id INT DEFAULT NULL, vma DOUBLE PRECISION DEFAULT NULL, cooper INT DEFAULT NULL, jongle_gauche INT DEFAULT NULL, jongle_droit INT DEFAULT NULL, jongle_tete INT DEFAULT NULL, date DATETIME DEFAULT NULL, demicooper INT DEFAULT NULL, conduiteballe VARCHAR(255) DEFAULT NULL, vitesse VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, INDEX IDX_BC04B641A76ED395 (user_id), INDEX IDX_BC04B64160E28355 (palier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_user (id INT AUTO_INCREMENT NOT NULL, equipe_id INT DEFAULT NULL, palier_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, poste_principal VARCHAR(255) DEFAULT NULL, poste_secondaire VARCHAR(255) DEFAULT NULL, poste_coord_x DOUBLE PRECISION DEFAULT NULL, poste_cord_y DOUBLE PRECISION DEFAULT NULL, profile_image VARCHAR(255) DEFAULT NULL, is_code_validated TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, last_connection DATETIME DEFAULT NULL, classement INT DEFAULT NULL, resp_phone VARCHAR(10) NOT NULL, palier_ended TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_38B383A1F85E0677 (username), INDEX IDX_38B383A16D861B89 (equipe_id), INDEX IDX_38B383A160E28355 (palier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_weight (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, date DATE NOT NULL, INDEX IDX_8A6B3CFEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_attendance ADD CONSTRAINT FK_4A04700A76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_attendance ADD CONSTRAINT FK_4A047007BA827B5 FOREIGN KEY (gathering_id) REFERENCES tbl_gathering (id)');
        $this->addSql('ALTER TABLE tbl_equipe ADD CONSTRAINT FK_A9EFD3AA12469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id)');
        $this->addSql('ALTER TABLE tbl_gathering ADD CONSTRAINT FK_84089C2D12469DE2 FOREIGN KEY (category_id) REFERENCES tbl_category (id)');
        $this->addSql('ALTER TABLE tbl_gathering ADD CONSTRAINT FK_84089C2D90B9D269 FOREIGN KEY (made_by_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_height ADD CONSTRAINT FK_78EB8CB0A76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_reset_password_request ADD CONSTRAINT FK_411E70CEA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_tests ADD CONSTRAINT FK_BC04B641A76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
        $this->addSql('ALTER TABLE tbl_tests ADD CONSTRAINT FK_BC04B64160E28355 FOREIGN KEY (palier_id) REFERENCES tbl_palier (id)');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A16D861B89 FOREIGN KEY (equipe_id) REFERENCES tbl_equipe (id)');
        $this->addSql('ALTER TABLE tbl_user ADD CONSTRAINT FK_38B383A160E28355 FOREIGN KEY (palier_id) REFERENCES tbl_palier (id)');
        $this->addSql('ALTER TABLE tbl_weight ADD CONSTRAINT FK_8A6B3CFEA76ED395 FOREIGN KEY (user_id) REFERENCES tbl_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_attendance DROP FOREIGN KEY FK_4A04700A76ED395');
        $this->addSql('ALTER TABLE tbl_attendance DROP FOREIGN KEY FK_4A047007BA827B5');
        $this->addSql('ALTER TABLE tbl_equipe DROP FOREIGN KEY FK_A9EFD3AA12469DE2');
        $this->addSql('ALTER TABLE tbl_gathering DROP FOREIGN KEY FK_84089C2D12469DE2');
        $this->addSql('ALTER TABLE tbl_gathering DROP FOREIGN KEY FK_84089C2D90B9D269');
        $this->addSql('ALTER TABLE tbl_height DROP FOREIGN KEY FK_78EB8CB0A76ED395');
        $this->addSql('ALTER TABLE tbl_reset_password_request DROP FOREIGN KEY FK_411E70CEA76ED395');
        $this->addSql('ALTER TABLE tbl_tests DROP FOREIGN KEY FK_BC04B641A76ED395');
        $this->addSql('ALTER TABLE tbl_tests DROP FOREIGN KEY FK_BC04B64160E28355');
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A16D861B89');
        $this->addSql('ALTER TABLE tbl_user DROP FOREIGN KEY FK_38B383A160E28355');
        $this->addSql('ALTER TABLE tbl_weight DROP FOREIGN KEY FK_8A6B3CFEA76ED395');
        $this->addSql('DROP TABLE tbl_attendance');
        $this->addSql('DROP TABLE tbl_category');
        $this->addSql('DROP TABLE tbl_chart_configuration');
        $this->addSql('DROP TABLE tbl_equipe');
        $this->addSql('DROP TABLE tbl_gathering');
        $this->addSql('DROP TABLE tbl_height');
        $this->addSql('DROP TABLE tbl_palier');
        $this->addSql('DROP TABLE tbl_player_code');
        $this->addSql('DROP TABLE tbl_reset_password_request');
        $this->addSql('DROP TABLE tbl_tests');
        $this->addSql('DROP TABLE tbl_user');
        $this->addSql('DROP TABLE tbl_weight');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
