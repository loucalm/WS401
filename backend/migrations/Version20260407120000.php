<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260407120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add icon column to activity_type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activity_type ADD icon VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activity_type DROP icon');
    }
}
