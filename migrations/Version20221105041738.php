<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221105041738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_compra ADD compra_id INT NOT NULL');
        $this->addSql('ALTER TABLE detalle_compra ADD CONSTRAINT FK_F219D258F2E704D7 FOREIGN KEY (compra_id) REFERENCES compra (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F219D258F2E704D7 ON detalle_compra (compra_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE detalle_compra DROP CONSTRAINT FK_F219D258F2E704D7');
        $this->addSql('DROP INDEX IDX_F219D258F2E704D7');
        $this->addSql('ALTER TABLE detalle_compra DROP compra_id');
    }
}
