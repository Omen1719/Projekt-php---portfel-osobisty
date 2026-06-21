<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add author (User) to categories and tags (per-user ownership).
 *
 * Existing rows are assigned to the first user; a user must therefore exist
 * before this migration runs on a populated database (the admin already does).
 */
final class Version20260609130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add author_id to categories and tags.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tags ADD author_id INT DEFAULT NULL');

        $this->addSql('UPDATE categories SET author_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) WHERE author_id IS NULL');
        $this->addSql('UPDATE tags SET author_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) WHERE author_id IS NULL');

        $this->addSql('ALTER TABLE categories MODIFY author_id INT NOT NULL');
        $this->addSql('ALTER TABLE tags MODIFY author_id INT NOT NULL');

        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_CATEGORIES_AUTHOR FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_TAGS_AUTHOR FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_CATEGORIES_AUTHOR ON categories (author_id)');
        $this->addSql('CREATE INDEX IDX_TAGS_AUTHOR ON tags (author_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_CATEGORIES_AUTHOR');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_TAGS_AUTHOR');
        $this->addSql('DROP INDEX IDX_CATEGORIES_AUTHOR ON categories');
        $this->addSql('DROP INDEX IDX_TAGS_AUTHOR ON tags');
        $this->addSql('ALTER TABLE categories DROP author_id');
        $this->addSql('ALTER TABLE tags DROP author_id');
    }
}
