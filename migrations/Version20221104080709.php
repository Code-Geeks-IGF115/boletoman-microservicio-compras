<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104080709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_compra DROP CONSTRAINT fk_f219d258f045077c');
        $this->addSql('ALTER TABLE detalle_compra DROP CONSTRAINT fk_f219d258f2e704d7');
        $this->addSql('DROP SEQUENCE compra_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE descuento_id_seq CASCADE');
        $this->addSql('DROP TABLE descuento');
        $this->addSql('DROP TABLE compra');
        $this->addSql('DROP INDEX uniq_f219d258f045077c');
        $this->addSql('DROP INDEX idx_f219d258f2e704d7');
        $this->addSql('ALTER TABLE detalle_compra ADD descripcion VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE detalle_compra DROP compra_id');
        $this->addSql('ALTER TABLE detalle_compra DROP descuento_id');
        $this->addSql('ALTER TABLE detalle_compra DROP categoria_butaca_id');
        $this->addSql('ALTER TABLE detalle_compra ALTER total TYPE NUMERIC(5, 2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE compra_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE descuento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE descuento (id INT NOT NULL, codigo VARCHAR(10) NOT NULL, descripcion VARCHAR(50) NOT NULL, monto NUMERIC(10, 2) NOT NULL, cantidad_butacas INT NOT NULL, categoria_butaca_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE compra (id INT NOT NULL, total NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE detalle_compra ADD compra_id INT NOT NULL');
        $this->addSql('ALTER TABLE detalle_compra ADD descuento_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE detalle_compra ADD categoria_butaca_id INT NOT NULL');
        $this->addSql('ALTER TABLE detalle_compra DROP descripcion');
        $this->addSql('ALTER TABLE detalle_compra ALTER total TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE detalle_compra ADD CONSTRAINT fk_f219d258f2e704d7 FOREIGN KEY (compra_id) REFERENCES compra (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE detalle_compra ADD CONSTRAINT fk_f219d258f045077c FOREIGN KEY (descuento_id) REFERENCES descuento (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_f219d258f045077c ON detalle_compra (descuento_id)');
        $this->addSql('CREATE INDEX idx_f219d258f2e704d7 ON detalle_compra (compra_id)');
    }
}
