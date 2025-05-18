<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518223320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log ADD medikament_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log ADD CONSTRAINT FK_D44E2A7C10D5C3EF FOREIGN KEY (medikament_id) REFERENCES medikament_liste (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D44E2A7C10D5C3EF ON medikament_log (medikament_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log DROP FOREIGN KEY FK_D44E2A7C10D5C3EF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D44E2A7C10D5C3EF ON medikament_log
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_log DROP medikament_id
        SQL);
    }
}
