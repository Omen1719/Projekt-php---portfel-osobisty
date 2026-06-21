<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Replace Task domain with Wallet + Operation.
 *
 * Drops tasks and tasks_tags, creates wallets and operations.
 * The tags table is kept (Tag has its own CRUD).
 */
final class Version20260603110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop tasks/tasks_tags, create wallets/operations (Task -> Wallet + Operation).';
    }

    public function up(Schema $schema): void
    {
        // Remove the Task domain.
        $this->addSql('ALTER TABLE tasks_tags DROP FOREIGN KEY FK_TASKS_TAGS_TASK');
        $this->addSql('ALTER TABLE tasks_tags DROP FOREIGN KEY FK_TASKS_TAGS_TAG');
        $this->addSql('DROP TABLE tasks_tags');
        $this->addSql('DROP TABLE tasks');

        // Create the Wallet domain.
        $this->addSql('CREATE TABLE wallets (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, type VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE operations (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, type VARCHAR(16) NOT NULL, date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT NOT NULL, wallet_id INT NOT NULL, INDEX IDX_OPERATIONS_CATEGORY (category_id), INDEX IDX_OPERATIONS_WALLET (wallet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_OPERATIONS_CATEGORY FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_OPERATIONS_WALLET FOREIGN KEY (wallet_id) REFERENCES wallets (id)');
    }

    public function down(Schema $schema): void
    {
        // Remove the Wallet domain.
        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_OPERATIONS_CATEGORY');
        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_OPERATIONS_WALLET');
        $this->addSql('DROP TABLE operations');
        $this->addSql('DROP TABLE wallets');

        // Restore the Task domain.
        $this->addSql('CREATE TABLE tasks (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT NOT NULL, INDEX IDX_5058659712469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_5058659712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE TABLE tasks_tags (task_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_TASKS_TAGS_TASK (task_id), INDEX IDX_TASKS_TAGS_TAG (tag_id), PRIMARY KEY (task_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tasks_tags ADD CONSTRAINT FK_TASKS_TAGS_TASK FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tasks_tags ADD CONSTRAINT FK_TASKS_TAGS_TAG FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
    }
}
