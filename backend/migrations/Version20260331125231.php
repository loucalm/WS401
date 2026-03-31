<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260331125231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, co2_factor DOUBLE PRECISION NOT NULL, unit_label VARCHAR(50) NOT NULL, category_id INT NOT NULL, INDEX IDX_8F1A8CBB12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE entry (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, total_co2 DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, activity_type_id INT NOT NULL, INDEX IDX_2B219D707E3C61F9 (owner_id), INDEX IDX_2B219D70C51EFA73 (activity_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE friendship (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, INDEX IDX_7234A45FF624B39D (sender_id), INDEX IDX_7234A45FCD53EDB6 (receiver_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity_type ADD CONSTRAINT FK_8F1A8CBB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D707E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70C51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (id)');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) DEFAULT NULL, ADD target_co2 DOUBLE PRECISION DEFAULT NULL, ADD unit_preference VARCHAR(20) NOT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_type DROP FOREIGN KEY FK_8F1A8CBB12469DE2');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D707E3C61F9');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70C51EFA73');
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FF624B39D');
        $this->addSql('ALTER TABLE friendship DROP FOREIGN KEY FK_7234A45FCD53EDB6');
        $this->addSql('DROP TABLE activity_type');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE friendship');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP username, DROP target_co2, DROP unit_preference, DROP latitude, DROP longitude');
    }
}
