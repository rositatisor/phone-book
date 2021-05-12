<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511152451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F773669A4AA658 FOREIGN KEY (guest_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_29F773669A4AA658 ON connection (guest_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F773669A4AA658');
        $this->addSql('DROP INDEX IDX_29F773669A4AA658 ON connection');
    }
}
