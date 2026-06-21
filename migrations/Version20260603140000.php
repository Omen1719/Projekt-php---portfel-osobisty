<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add note (Markdown) column to operations.
 */
final class Version20260603140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add note column to operations (Markdown description).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE operations ADD note LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE operations DROP note');
    }
}
