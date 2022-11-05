<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104083654 extends AbstractMigration
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
        $this->addSql('CREATE TABLE compra (id INT NOT NULL, total NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE descuento (id INT NOT NULL, codigo VARCHAR(100) NOT NULL, descripcion VARCHAR(100) NOT NULL, monto NUMERIC(5, 2) NOT NULL, cantidad_butacas INT NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE compra_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE descuento_id_seq CASCADE');
        $this->addSql('DROP TABLE compra');
        $this->addSql('DROP TABLE descuento');
    }
}
