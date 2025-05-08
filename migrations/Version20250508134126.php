<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508134126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log CHANGE taget_status taget_status VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD telefon_nummer VARCHAR(255) DEFAULT NULL, ADD addresse VARCHAR(255) DEFAULT NULL, ADD by_navn VARCHAR(255) DEFAULT NULL, ADD postnummer VARCHAR(255) DEFAULT NULL, ADD land VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP telefon_nummer, DROP addresse, DROP by_navn, DROP postnummer, DROP land
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log CHANGE taget_status taget_status TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
    }
}
