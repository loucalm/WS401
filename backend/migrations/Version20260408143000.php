<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing profile_picture column on user table';
    }

    public function up(Schema $schema): void
    {
        $userTable = $schema->getTable('user');

        if (!$userTable->hasColumn('profile_picture')) {
            $this->addSql('ALTER TABLE user ADD profile_picture VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $userTable = $schema->getTable('user');

        if ($userTable->hasColumn('profile_picture')) {
            $this->addSql('ALTER TABLE user DROP profile_picture');
        }
    }
}
