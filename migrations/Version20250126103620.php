<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250126103620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE68D9F6D38');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT order_product_pkey');
        $this->addSql('ALTER TABLE order_product ADD id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE order_product ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES "products" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT fk_2530ade68d9f6d38');
        $this->addSql('ALTER TABLE order_product DROP CONSTRAINT fk_2530ade64584665a');
        $this->addSql('DROP INDEX order_product_pkey');
        $this->addSql('ALTER TABLE order_product DROP id');
        $this->addSql('ALTER TABLE order_product DROP quantity');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT fk_2530ade68d9f6d38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT fk_2530ade64584665a FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_product ADD PRIMARY KEY (order_id, product_id)');
    }
}
