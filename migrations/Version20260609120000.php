<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create operations_tags join table (Operation <-> Tag many-to-many).
 */
final class Version20260609120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create operations_tags table (Operation <-> Tag many-to-many).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE operations_tags (operation_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_OPERATIONS_TAGS_OPERATION (operation_id), INDEX IDX_OPERATIONS_TAGS_TAG (tag_id), PRIMARY KEY (operation_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE operations_tags ADD CONSTRAINT FK_OPERATIONS_TAGS_OPERATION FOREIGN KEY (operation_id) REFERENCES operations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operations_tags ADD CONSTRAINT FK_OPERATIONS_TAGS_TAG FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE operations_tags DROP FOREIGN KEY FK_OPERATIONS_TAGS_OPERATION');
        $this->addSql('ALTER TABLE operations_tags DROP FOREIGN KEY FK_OPERATIONS_TAGS_TAG');
        $this->addSql('DROP TABLE operations_tags');
    }
}
