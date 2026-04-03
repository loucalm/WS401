<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403151000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing sub_category and diet columns to activity_type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activity_type ADD sub_category VARCHAR(255) DEFAULT NULL, ADD diet VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activity_type DROP sub_category, DROP diet');
    }
}
