<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514211018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE medikament_alarm_log (id INT AUTO_INCREMENT NOT NULL, medikament_navn VARCHAR(255) NOT NULL, dato VARCHAR(255) NOT NULL, tidspunkt VARCHAR(5) NOT NULL, userId INT NOT NULL, INDEX IDX_6F508DC564B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_alarm_log ADD CONSTRAINT FK_6F508DC564B64DCC FOREIGN KEY (userId) REFERENCES user (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE medikament_alarm_log DROP FOREIGN KEY FK_6F508DC564B64DCC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE medikament_alarm_log
        SQL);
    }
}
