<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Align index names with Doctrine's generated names so the schema validates cleanly.
 */
final class Version20260621120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename indexes to Doctrine-generated names (schema:validate sync).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories RENAME INDEX idx_categories_author TO IDX_3AF34668F675F31B');
        $this->addSql('ALTER TABLE operations RENAME INDEX idx_operations_category TO IDX_2814534812469DE2');
        $this->addSql('ALTER TABLE operations RENAME INDEX idx_operations_wallet TO IDX_28145348712520F3');
        $this->addSql('ALTER TABLE operations RENAME INDEX idx_operations_author TO IDX_28145348F675F31B');
        $this->addSql('ALTER TABLE operations_tags RENAME INDEX idx_operations_tags_operation TO IDX_C5D0F2FF44AC3583');
        $this->addSql('ALTER TABLE operations_tags RENAME INDEX idx_operations_tags_tag TO IDX_C5D0F2FFBAD26311');
        $this->addSql('ALTER TABLE tags RENAME INDEX idx_tags_author TO IDX_6FBC9426F675F31B');
        $this->addSql('ALTER TABLE wallets RENAME INDEX idx_wallets_author TO IDX_967AAA6CF675F31B');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories RENAME INDEX IDX_3AF34668F675F31B TO IDX_CATEGORIES_AUTHOR');
        $this->addSql('ALTER TABLE operations RENAME INDEX IDX_2814534812469DE2 TO IDX_OPERATIONS_CATEGORY');
        $this->addSql('ALTER TABLE operations RENAME INDEX IDX_28145348712520F3 TO IDX_OPERATIONS_WALLET');
        $this->addSql('ALTER TABLE operations RENAME INDEX IDX_28145348F675F31B TO IDX_OPERATIONS_AUTHOR');
        $this->addSql('ALTER TABLE operations_tags RENAME INDEX IDX_C5D0F2FF44AC3583 TO IDX_OPERATIONS_TAGS_OPERATION');
        $this->addSql('ALTER TABLE operations_tags RENAME INDEX IDX_C5D0F2FFBAD26311 TO IDX_OPERATIONS_TAGS_TAG');
        $this->addSql('ALTER TABLE tags RENAME INDEX IDX_6FBC9426F675F31B TO IDX_TAGS_AUTHOR');
        $this->addSql('ALTER TABLE wallets RENAME INDEX IDX_967AAA6CF675F31B TO IDX_WALLETS_AUTHOR');
    }
}
