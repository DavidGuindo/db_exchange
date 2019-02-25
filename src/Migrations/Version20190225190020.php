<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190225190020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE message DROP check_read');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2B34B90EE');
        $this->addSql('DROP INDEX IDX_E19D9AD2B34B90EE ON service');
        $this->addSql('ALTER TABLE service DROP user_offer_id');
     //   $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE message ADD check_read TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE service ADD user_offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2B34B90EE FOREIGN KEY (user_offer_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2B34B90EE ON service (user_offer_id)');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
