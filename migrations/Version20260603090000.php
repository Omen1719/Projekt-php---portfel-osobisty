<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add slug column to categories (Gedmo Sluggable).
 */
final class Version20260603090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug column to categories (Gedmo Sluggable).';
    }

    public function up(Schema $schema): void
    {
        // 1. Add slug as nullable first, so existing rows are not rejected.
        $this->addSql('ALTER TABLE categories ADD slug VARCHAR(64) DEFAULT NULL');
        // 2. Backfill a unique slug for existing rows. Gedmo will generate proper
        //    slugs from the title for any newly created or edited category.
        $this->addSql("UPDATE categories SET slug = CONCAT('category-', id) WHERE slug IS NULL");
        // 3. Now that every row has a value, enforce NOT NULL and uniqueness.
        $this->addSql('ALTER TABLE categories MODIFY slug VARCHAR(64) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uq_categories_slug ON categories (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uq_categories_slug ON categories');
        $this->addSql('ALTER TABLE categories DROP slug');
    }
}
