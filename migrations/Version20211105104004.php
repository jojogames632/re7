<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211105104004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe_food (id INT AUTO_INCREMENT NOT NULL, recipe_id_id INT NOT NULL, food_id_id INT NOT NULL, quantity INT NOT NULL, unit VARCHAR(255) NOT NULL, INDEX IDX_AB23732869574A48 (recipe_id_id), INDEX IDX_AB2373288E255BBD (food_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB23732869574A48 FOREIGN KEY (recipe_id_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB2373288E255BBD FOREIGN KEY (food_id_id) REFERENCES food (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE recipe_food');
    }
}
