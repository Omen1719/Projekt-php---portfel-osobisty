<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add author (User) to wallets and operations (Security Voters / per-user ownership).
 *
 * IMPORTANT: at least one user must exist before this migration runs on a database
 * that already contains wallets/operations (existing rows are assigned to the first user).
 */
final class Version20260603130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add author_id to wallets and operations.';
    }

    public function up(Schema $schema): void
    {
        // 1. Add nullable columns so existing rows are not rejected.
        $this->addSql('ALTER TABLE wallets ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operations ADD author_id INT DEFAULT NULL');

        // 2. Assign existing rows to the first user (the admin).
        $this->addSql('UPDATE wallets SET author_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) WHERE author_id IS NULL');
        $this->addSql('UPDATE operations SET author_id = (SELECT id FROM users ORDER BY id ASC LIMIT 1) WHERE author_id IS NULL');

        // 3. Enforce NOT NULL, foreign keys and indexes.
        $this->addSql('ALTER TABLE wallets MODIFY author_id INT NOT NULL');
        $this->addSql('ALTER TABLE operations MODIFY author_id INT NOT NULL');
        $this->addSql('ALTER TABLE wallets ADD CONSTRAINT FK_WALLETS_AUTHOR FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_OPERATIONS_AUTHOR FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_WALLETS_AUTHOR ON wallets (author_id)');
        $this->addSql('CREATE INDEX IDX_OPERATIONS_AUTHOR ON operations (author_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wallets DROP FOREIGN KEY FK_WALLETS_AUTHOR');
        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_OPERATIONS_AUTHOR');
        $this->addSql('DROP INDEX IDX_WALLETS_AUTHOR ON wallets');
        $this->addSql('DROP INDEX IDX_OPERATIONS_AUTHOR ON operations');
        $this->addSql('ALTER TABLE wallets DROP author_id');
        $this->addSql('ALTER TABLE operations DROP author_id');
    }
}
