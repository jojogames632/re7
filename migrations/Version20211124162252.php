<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124162252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus ADD section_id INT NOT NULL');
        $this->addSql('ALTER TABLE bonus ADD CONSTRAINT FK_9F987F7AD823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('CREATE INDEX IDX_9F987F7AD823E37A ON bonus (section_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP FOREIGN KEY FK_9F987F7AD823E37A');
        $this->addSql('DROP INDEX IDX_9F987F7AD823E37A ON bonus');
        $this->addSql('ALTER TABLE bonus DROP section_id');
    }
}
