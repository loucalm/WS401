<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260407130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add details JSON column to entry table';
    }

    public function up(Schema $schema): void
    {
        $entryTable = $schema->getTable('entry');

        if (!$entryTable->hasColumn('details')) {
            $this->addSql('ALTER TABLE entry ADD details JSON DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $entryTable = $schema->getTable('entry');

        if ($entryTable->hasColumn('details')) {
            $this->addSql('ALTER TABLE entry DROP details');
        }
    }
}
