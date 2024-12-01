<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241130015449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produtos (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(100) DEFAULT NULL, preco NUMERIC(10, 0) DEFAULT NULL, descricao VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transacoes (id INT AUTO_INCREMENT NOT NULL, id_produto_id INT NOT NULL, id_transacao VARCHAR(255) DEFAULT NULL, status VARCHAR(20) DEFAULT NULL, metodo_pagamento VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_97CF7B5CB5D67D81 (id_produto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transacoes ADD CONSTRAINT FK_97CF7B5CB5D67D81 FOREIGN KEY (id_produto_id) REFERENCES produtos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transacoes DROP FOREIGN KEY FK_97CF7B5CB5D67D81');
        $this->addSql('DROP TABLE produtos');
        $this->addSql('DROP TABLE transacoes');
    }
}
