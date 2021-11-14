<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211113092453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_food ADD section_id INT NOT NULL, DROP section');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB237328D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('CREATE INDEX IDX_AB237328D823E37A ON recipe_food (section_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB237328D823E37A');
        $this->addSql('DROP INDEX IDX_AB237328D823E37A ON recipe_food');
        $this->addSql('ALTER TABLE recipe_food ADD section VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP section_id');
    }
}
