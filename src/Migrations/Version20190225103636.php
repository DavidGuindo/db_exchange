<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190225103636 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE service ADD city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD28BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD28BAC62AF ON service (city_id)');
     //   $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD28BAC62AF');
        $this->addSql('DROP INDEX IDX_E19D9AD28BAC62AF ON service');
        $this->addSql('ALTER TABLE service DROP city_id');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
