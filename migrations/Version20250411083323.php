<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411083323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs;
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE fulde_navn fulde_navn VARCHAR(255) NOT NULL, CHANGE cpr cpr VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE fulde_navn fulde_navn VARCHAR(255) DEFAULT NULL, CHANGE cpr cpr VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_liste DROP FOREIGN KEY FK_512744C864B64DCC
        SQL);
    }
}
