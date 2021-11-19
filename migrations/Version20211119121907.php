<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119121907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_food ADD unit_id INT DEFAULT NULL, DROP unit');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB237328F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('CREATE INDEX IDX_AB237328F8BD700D ON recipe_food (unit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB237328F8BD700D');
        $this->addSql('DROP INDEX IDX_AB237328F8BD700D ON recipe_food');
        $this->addSql('ALTER TABLE recipe_food ADD unit VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP unit_id');
    }
}
