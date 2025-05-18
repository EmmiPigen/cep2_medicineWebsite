<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518204016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, userId INT NOT NULL, INDEX IDX_7CE748A64B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748A64B64DCC FOREIGN KEY (userId) REFERENCES user (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD addresse VARCHAR(255) DEFAULT NULL, ADD by_navn VARCHAR(255) DEFAULT NULL, ADD postnummer VARCHAR(255) DEFAULT NULL, ADD land VARCHAR(255) DEFAULT NULL, CHANGE telefonnummer telefonnummer VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748A64B64DCC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP addresse, DROP by_navn, DROP postnummer, DROP land, CHANGE telefonnummer telefonnummer VARCHAR(20) DEFAULT NULL
        SQL);
    }
}
