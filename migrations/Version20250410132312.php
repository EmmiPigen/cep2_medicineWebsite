<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410132312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
          CREATE TABLE user (user_id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(userId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
          CREATE TABLE medikament_log (id INT AUTO_INCREMENT NOT NULL, medikament_navn VARCHAR(255) NOT NULL, taget_tid DATETIME DEFAULT NULL, taget_status VARCHAR(255) NOT NULL, taget_lokale VARCHAR(255) NOT NULL, userId INT NOT NULL, INDEX IDX_D44E2A7C64B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE udstyr (id INT AUTO_INCREMENT NOT NULL, lokale VARCHAR(255) NOT NULL, enhed VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, power INT NOT NULL, userId INT NOT NULL, INDEX IDX_EC1BF10D64B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE medikament_liste (id INT AUTO_INCREMENT NOT NULL, medikament_navn VARCHAR(255) NOT NULL, tidspunkter_tages LONGTEXT NOT NULL, time_interval INT NOT NULL, dosis INT NOT NULL, enhed VARCHAR(4) NOT NULL, userId INT NOT NULL, INDEX IDX_512744C864B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log ADD CONSTRAINT FK_D44E2A7C64B64DCC FOREIGN KEY (userId) REFERENCES user (userId)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE udstyr ADD CONSTRAINT FK_EC1BF10D64B64DCC FOREIGN KEY (userId) REFERENCES user (userId)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD fulde_navn VARCHAR(255) NOT NULL, ADD cpr VARCHAR(255) NOT NULL
        SQL);
    }
}
