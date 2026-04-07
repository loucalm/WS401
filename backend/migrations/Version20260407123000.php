<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260407123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase category color column length';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category CHANGE color color VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category CHANGE color color VARCHAR(7) NOT NULL');
    }
}
