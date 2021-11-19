<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119091800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe CHANGE duration duration DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1372609125B FOREIGN KEY (cooking_type_id) REFERENCES cooking_type (id)');
        $this->addSql('CREATE INDEX IDX_DA88B137C54C8C93 ON recipe (type_id)');
        $this->addSql('CREATE INDEX IDX_DA88B1372609125B ON recipe (cooking_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137C54C8C93');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B1372609125B');
        $this->addSql('DROP INDEX IDX_DA88B137C54C8C93 ON recipe');
        $this->addSql('DROP INDEX IDX_DA88B1372609125B ON recipe');
        $this->addSql('ALTER TABLE recipe CHANGE duration duration INT NOT NULL');
    }
}
