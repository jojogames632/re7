<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211105104153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB23732869574A48');
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB2373288E255BBD');
        $this->addSql('DROP INDEX IDX_AB23732869574A48 ON recipe_food');
        $this->addSql('DROP INDEX IDX_AB2373288E255BBD ON recipe_food');
        $this->addSql('ALTER TABLE recipe_food ADD recipe_id INT NOT NULL, ADD food_id INT NOT NULL, DROP recipe_id_id, DROP food_id_id');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB23732859D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB237328BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('CREATE INDEX IDX_AB23732859D8A214 ON recipe_food (recipe_id)');
        $this->addSql('CREATE INDEX IDX_AB237328BA8E87C4 ON recipe_food (food_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB23732859D8A214');
        $this->addSql('ALTER TABLE recipe_food DROP FOREIGN KEY FK_AB237328BA8E87C4');
        $this->addSql('DROP INDEX IDX_AB23732859D8A214 ON recipe_food');
        $this->addSql('DROP INDEX IDX_AB237328BA8E87C4 ON recipe_food');
        $this->addSql('ALTER TABLE recipe_food ADD recipe_id_id INT NOT NULL, ADD food_id_id INT NOT NULL, DROP recipe_id, DROP food_id');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB23732869574A48 FOREIGN KEY (recipe_id_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_food ADD CONSTRAINT FK_AB2373288E255BBD FOREIGN KEY (food_id_id) REFERENCES food (id)');
        $this->addSql('CREATE INDEX IDX_AB23732869574A48 ON recipe_food (recipe_id_id)');
        $this->addSql('CREATE INDEX IDX_AB2373288E255BBD ON recipe_food (food_id_id)');
    }
}
