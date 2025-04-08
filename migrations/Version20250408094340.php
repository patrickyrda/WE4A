<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408094340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE inscriptions (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, ue_id_id INT NOT NULL, INDEX IDX_74E0281C9D86650F (user_id_id), INDEX IDX_74E0281C1CA2F0B7 (ue_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C1CA2F0B7 FOREIGN KEY (ue_id_id) REFERENCES ue (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C1CA2F0B7
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inscriptions
        SQL);
    }
}
