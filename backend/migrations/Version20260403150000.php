<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create entry_item table for EntryItem entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE entry_item (id INT AUTO_INCREMENT NOT NULL, quantity DOUBLE PRECISION NOT NULL, entry_id INT NOT NULL, activity_type_id INT NOT NULL, INDEX IDX_BEF9162F2E635F0C (entry_id), INDEX IDX_BEF9162FC51EFA73 (activity_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entry_item ADD CONSTRAINT FK_BEF9162F2E635F0C FOREIGN KEY (entry_id) REFERENCES entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entry_item ADD CONSTRAINT FK_BEF9162FC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entry_item DROP FOREIGN KEY FK_BEF9162F2E635F0C');
        $this->addSql('ALTER TABLE entry_item DROP FOREIGN KEY FK_BEF9162FC51EFA73');
        $this->addSql('DROP TABLE entry_item');
    }
}
