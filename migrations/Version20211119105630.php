<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119105630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D43829F75E237E06 ON food (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D737AEF5E237E06 ON section (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8CDE57295E237E06 ON type (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D43829F75E237E06 ON food');
        $this->addSql('DROP INDEX UNIQ_2D737AEF5E237E06 ON section');
        $this->addSql('DROP INDEX UNIQ_8CDE57295E237E06 ON type');
    }
}
