<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240520160853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station CHANGE identifier identifier VARCHAR(255) DEFAULT NULL, CHANGE organizer organizer VARCHAR(255) DEFAULT NULL, CHANGE location_lng location_lng DOUBLE PRECISION DEFAULT NULL, CHANGE location_lat location_lat DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD username VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, CHANGE location_lng location_lng DOUBLE PRECISION DEFAULT NULL, CHANGE location_lat location_lat DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON team (username)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE station CHANGE identifier identifier VARCHAR(255) DEFAULT \'NULL\', CHANGE organizer organizer VARCHAR(255) DEFAULT \'NULL\', CHANGE location_lng location_lng DOUBLE PRECISION DEFAULT \'NULL\', CHANGE location_lat location_lat DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERNAME ON team');
        $this->addSql('ALTER TABLE team DROP username, DROP roles, DROP password, CHANGE location_lng location_lng DOUBLE PRECISION DEFAULT \'NULL\', CHANGE location_lat location_lat DOUBLE PRECISION DEFAULT \'NULL\'');
    }
}
