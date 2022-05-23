<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220522153329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activitat (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, objectiu VARCHAR(255) NOT NULL, interior TINYINT(1) NOT NULL, descripcio TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activitat_programada (id INT AUTO_INCREMENT NOT NULL, equip_id INT NOT NULL, nom VARCHAR(255) NOT NULL, objectiu VARCHAR(255) NOT NULL, interior TINYINT(1) NOT NULL, descripcio LONGTEXT DEFAULT NULL, data_ini DATETIME NOT NULL, data_fi DATETIME NOT NULL, INDEX IDX_3FEBD5678AC49FD9 (equip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equip (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equip_monitor (equip_id INT NOT NULL, monitor_id INT NOT NULL, INDEX IDX_11C571B8AC49FD9 (equip_id), INDEX IDX_11C571B4CE1C902 (monitor_id), PRIMARY KEY(equip_id, monitor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monitor (id INT AUTO_INCREMENT NOT NULL, persona_id INT NOT NULL, email VARCHAR(255) NOT NULL, contrasenya VARCHAR(255) NOT NULL, llicencia INT DEFAULT NULL, targeta_sanitaria VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E1159985F5F88DB9 (persona_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, persona_id INT NOT NULL, equip_id INT DEFAULT NULL, autoritzacio TINYINT(1) NOT NULL, data_naixement DATE NOT NULL, targeta_sanitaria VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D79F6B11F5F88DB9 (persona_id), INDEX IDX_D79F6B118AC49FD9 (equip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persona (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, cognom1 VARCHAR(255) NOT NULL, cognom2 VARCHAR(255) DEFAULT NULL, dni VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsable (id INT AUTO_INCREMENT NOT NULL, persona_id INT NOT NULL, participant_id INT NOT NULL, telefon1 INT NOT NULL, telefon2 INT DEFAULT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_52520D07F5F88DB9 (persona_id), UNIQUE INDEX UNIQ_52520D079D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activitat_programada ADD CONSTRAINT FK_3FEBD5678AC49FD9 FOREIGN KEY (equip_id) REFERENCES equip (id)');
        $this->addSql('ALTER TABLE equip_monitor ADD CONSTRAINT FK_11C571B8AC49FD9 FOREIGN KEY (equip_id) REFERENCES equip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equip_monitor ADD CONSTRAINT FK_11C571B4CE1C902 FOREIGN KEY (monitor_id) REFERENCES monitor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monitor ADD CONSTRAINT FK_E1159985F5F88DB9 FOREIGN KEY (persona_id) REFERENCES persona (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11F5F88DB9 FOREIGN KEY (persona_id) REFERENCES persona (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B118AC49FD9 FOREIGN KEY (equip_id) REFERENCES equip (id)');
        $this->addSql('ALTER TABLE responsable ADD CONSTRAINT FK_52520D07F5F88DB9 FOREIGN KEY (persona_id) REFERENCES persona (id)');
        $this->addSql('ALTER TABLE responsable ADD CONSTRAINT FK_52520D079D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activitat_programada DROP FOREIGN KEY FK_3FEBD5678AC49FD9');
        $this->addSql('ALTER TABLE equip_monitor DROP FOREIGN KEY FK_11C571B8AC49FD9');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B118AC49FD9');
        $this->addSql('ALTER TABLE equip_monitor DROP FOREIGN KEY FK_11C571B4CE1C902');
        $this->addSql('ALTER TABLE responsable DROP FOREIGN KEY FK_52520D079D1C3019');
        $this->addSql('ALTER TABLE monitor DROP FOREIGN KEY FK_E1159985F5F88DB9');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11F5F88DB9');
        $this->addSql('ALTER TABLE responsable DROP FOREIGN KEY FK_52520D07F5F88DB9');
        $this->addSql('DROP TABLE activitat');
        $this->addSql('DROP TABLE activitat_programada');
        $this->addSql('DROP TABLE equip');
        $this->addSql('DROP TABLE equip_monitor');
        $this->addSql('DROP TABLE monitor');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE persona');
        $this->addSql('DROP TABLE responsable');
    }
}
