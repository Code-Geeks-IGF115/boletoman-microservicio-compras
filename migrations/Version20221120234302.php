<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221120234302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE compra_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE descuento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE detalle_compra_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE compra (id INT NOT NULL, total NUMERIC(5, 2) NOT NULL, id_usuario INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE descuento (id INT NOT NULL, codigo VARCHAR(100) NOT NULL, descripcion VARCHAR(100) NOT NULL, monto NUMERIC(5, 2) NOT NULL, cantidad_butacas INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE detalle_compra (id INT NOT NULL, compra_id INT NOT NULL, cantidad INT NOT NULL, total NUMERIC(5, 2) NOT NULL, descripcion VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F219D258F2E704D7 ON detalle_compra (compra_id)');
        $this->addSql('ALTER TABLE detalle_compra ADD CONSTRAINT FK_F219D258F2E704D7 FOREIGN KEY (compra_id) REFERENCES compra (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE compra_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE descuento_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE detalle_compra_id_seq CASCADE');
        $this->addSql('ALTER TABLE detalle_compra DROP CONSTRAINT FK_F219D258F2E704D7');
        $this->addSql('DROP TABLE compra');
        $this->addSql('DROP TABLE descuento');
        $this->addSql('DROP TABLE detalle_compra');
    }
}
