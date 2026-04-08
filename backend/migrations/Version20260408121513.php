<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408121513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entry_item DROP FOREIGN KEY `FK_BEF9162F2E635F0C`');
        $this->addSql('ALTER TABLE entry_item ADD CONSTRAINT FK_BB395D0DBA364942 FOREIGN KEY (entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE entry_item RENAME INDEX idx_bef9162f2e635f0c TO IDX_BB395D0DBA364942');
        $this->addSql('ALTER TABLE entry_item RENAME INDEX idx_bef9162fc51efa73 TO IDX_BB395D0DC51EFA73');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entry_item DROP FOREIGN KEY FK_BB395D0DBA364942');
        $this->addSql('ALTER TABLE entry_item ADD CONSTRAINT `FK_BEF9162F2E635F0C` FOREIGN KEY (entry_id) REFERENCES entry (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entry_item RENAME INDEX idx_bb395d0dba364942 TO IDX_BEF9162F2E635F0C');
        $this->addSql('ALTER TABLE entry_item RENAME INDEX idx_bb395d0dc51efa73 TO IDX_BEF9162FC51EFA73');
    }
}
