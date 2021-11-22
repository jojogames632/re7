<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122091651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cooking_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FEA39085E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D43829F75E237E06 (name), INDEX IDX_D43829F7D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, midday_recipe_id INT DEFAULT NULL, evening_recipe_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, midday_persons INT DEFAULT NULL, evening_persons INT DEFAULT NULL, owner VARCHAR(255) NOT NULL, INDEX IDX_D499BFF672914193 (midday_recipe_id), INDEX IDX_D499BFF632883667 (evening_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, type_id INT NOT NULL, cooking_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, persons INT NOT NULL, duration VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DA88B1375E237E06 (name), INDEX IDX_DA88B13712469DE2 (category_id), INDEX IDX_DA88B137C54C8C93 (type_id), INDEX IDX_DA88B1372609125B (cooking_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_food (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, food_id INT NOT NULL, section_id INT NOT NULL, unit_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, food_name VARCHAR(255) NOT NULL, persons INT NOT NULL, INDEX IDX_AB23732859D8A214 (recipe_id), INDEX IDX_AB237328BA8E87C4 (food_id), INDEX IDX_AB237328D823E37A (section_id), INDEX IDX_AB237328F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2D737AEF5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, food_id INT NOT NULL, unit_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, owner VARCHAR(255) NOT NULL, INDEX IDX_FB45F439D823E37A (section_id), INDEX IDX_FB45F439BA8E87C4 (food_id), INDEX IDX_FB45F439F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8CDE57295E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DCBB0C535E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF672914193 FOREIGN KEY (midday_recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF632883667 FOREIGN KEY (evening_recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B13712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1372609125B FOREIGN KEY (cooking_type_id) REFERENCES cooking_type (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB23732859D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB237328BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB237328D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB237328F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE shopping ADD CONSTRAINT FK_FB45F439D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE shopping ADD CONSTRAINT FK_FB45F439BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE shopping ADD CONSTRAINT FK_FB45F439F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B13712469DE2');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B1372609125B');
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB237328BA8E87C4');
        $this->addSql('ALTER TABLE shopping DROP FOREIGN KEY FK_FB45F439BA8E87C4');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF672914193');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF632883667');
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB23732859D8A214');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7D823E37A');
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB237328D823E37A');
        $this->addSql('ALTER TABLE shopping DROP FOREIGN KEY FK_FB45F439D823E37A');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137C54C8C93');
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB237328F8BD700D');
        $this->addSql('ALTER TABLE shopping DROP FOREIGN KEY FK_FB45F439F8BD700D');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE cooking_type');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_food');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE shopping');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE unit');
    }
}
