<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403150500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove obsolete activity_type relation from entry table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70C51EFA73');
        $this->addSql('DROP INDEX IDX_2B219D70C51EFA73 ON entry');
        $this->addSql('ALTER TABLE entry DROP activity_type_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entry ADD activity_type_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_2B219D70C51EFA73 ON entry (activity_type_id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70C51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (id)');
    }
}
