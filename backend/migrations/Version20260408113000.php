<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Grant ROLE_ADMIN to the seeded admin account';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE `user` SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'admin@test.com'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE `user` SET roles = '[]' WHERE email = 'admin@test.com'");
    }
}