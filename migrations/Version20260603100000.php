<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create tags table and tasks_tags join table (Task <-> Tag many-to-many).
 */
final class Version20260603100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tags and tasks_tags tables (Task <-> Tag many-to-many).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX uq_tags_title (title), UNIQUE INDEX uq_tags_slug (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tasks_tags (task_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_TASKS_TAGS_TASK (task_id), INDEX IDX_TASKS_TAGS_TAG (tag_id), PRIMARY KEY (task_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tasks_tags ADD CONSTRAINT FK_TASKS_TAGS_TASK FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tasks_tags ADD CONSTRAINT FK_TASKS_TAGS_TAG FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tasks_tags DROP FOREIGN KEY FK_TASKS_TAGS_TASK');
        $this->addSql('ALTER TABLE tasks_tags DROP FOREIGN KEY FK_TASKS_TAGS_TAG');
        $this->addSql('DROP TABLE tasks_tags');
        $this->addSql('DROP TABLE tags');
    }
}
