<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508092236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE time_tracker (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL, start_time TIME NOT NULL, end_time TIME DEFAULT NULL, INDEX IDX_F8E48BC9166D1F9C (project_id), INDEX IDX_F8E48BC9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE time_tracker ADD CONSTRAINT FK_F8E48BC9166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE time_tracker ADD CONSTRAINT FK_F8E48BC9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE time_tracker DROP FOREIGN KEY FK_F8E48BC9166D1F9C');
        $this->addSql('ALTER TABLE time_tracker DROP FOREIGN KEY FK_F8E48BC9A76ED395');
        $this->addSql('DROP TABLE time_tracker');
    }
}
