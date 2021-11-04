<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104102622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, section VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, midday_recipe_id INT DEFAULT NULL, evening_recipe_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D499BFF672914193 (midday_recipe_id), INDEX IDX_D499BFF632883667 (evening_recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, duration INT NOT NULL, persons INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF672914193 FOREIGN KEY (midday_recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF632883667 FOREIGN KEY (evening_recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF672914193');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF632883667');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE recipe');
    }
}
