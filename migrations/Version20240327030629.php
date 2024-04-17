<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240327030629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande CHANGE idcommande idcommande INT AUTO_INCREMENT NOT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conseil DROP FOREIGN KEY conseil_produit');
        $this->addSql('ALTER TABLE conseil DROP FOREIGN KEY conseil_categorie');
        $this->addSql('ALTER TABLE conseil CHANGE id_produit id_produit INT DEFAULT NULL, CHANGE video video VARCHAR(255) DEFAULT NULL, CHANGE id_typeC id_typeC INT DEFAULT NULL');
        $this->addSql('DROP INDEX id_typec ON conseil');
        $this->addSql('CREATE INDEX IDX_3F3F0681916CF8AC ON conseil (id_typeC)');
        $this->addSql('DROP INDEX conseil_produit ON conseil');
        $this->addSql('CREATE INDEX IDX_3F3F0681F7384557 ON conseil (id_produit)');
        $this->addSql('ALTER TABLE conseil ADD CONSTRAINT conseil_produit FOREIGN KEY (id_produit) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE conseil ADD CONSTRAINT conseil_categorie FOREIGN KEY (id_typeC) REFERENCES typeconseil (idTypeC)');
        $this->addSql('ALTER TABLE favorite_conseil CHANGE id_conseil id_conseil INT DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre CHANGE id_evenement_offre id_evenement_offre INT DEFAULT NULL');
        $this->addSql('ALTER TABLE panier CHANGE id_produit id_produit INT DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prod CHANGE id_categorie id_categorie INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY nom_categorie');
        $this->addSql('ALTER TABLE produit CHANGE id_categorie id_categorie INT DEFAULT NULL');
        $this->addSql('DROP INDEX id_categorie ON produit');
        $this->addSql('CREATE INDEX IDX_29A5EC27C9486A13 ON produit (id_categorie)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT nom_categorie FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie)');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY rev_con');
        $this->addSql('ALTER TABLE review CHANGE id_conseil id_conseil INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6E1A2AFF1 FOREIGN KEY (id_conseil) REFERENCES conseil (id_conseil)');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE commande CHANGE idcommande idcommande INT NOT NULL, CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE conseil DROP FOREIGN KEY FK_3F3F0681916CF8AC');
        $this->addSql('ALTER TABLE conseil DROP FOREIGN KEY FK_3F3F0681F7384557');
        $this->addSql('ALTER TABLE conseil CHANGE id_produit id_produit INT NOT NULL, CHANGE video video VARCHAR(255) NOT NULL, CHANGE id_typeC id_typeC INT NOT NULL');
        $this->addSql('DROP INDEX idx_3f3f0681916cf8ac ON conseil');
        $this->addSql('CREATE INDEX id_typeC ON conseil (id_typeC)');
        $this->addSql('DROP INDEX idx_3f3f0681f7384557 ON conseil');
        $this->addSql('CREATE INDEX conseil_produit ON conseil (id_produit)');
        $this->addSql('ALTER TABLE conseil ADD CONSTRAINT FK_3F3F0681916CF8AC FOREIGN KEY (id_typeC) REFERENCES typeconseil (idTypeC)');
        $this->addSql('ALTER TABLE conseil ADD CONSTRAINT FK_3F3F0681F7384557 FOREIGN KEY (id_produit) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE favorite_conseil CHANGE id_user id_user INT NOT NULL, CHANGE id_conseil id_conseil INT NOT NULL');
        $this->addSql('ALTER TABLE offre CHANGE id_evenement_offre id_evenement_offre INT NOT NULL');
        $this->addSql('ALTER TABLE panier CHANGE id_user id_user INT NOT NULL, CHANGE id_produit id_produit INT NOT NULL');
        $this->addSql('ALTER TABLE prod CHANGE id_categorie id_categorie INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27C9486A13');
        $this->addSql('ALTER TABLE produit CHANGE id_categorie id_categorie INT NOT NULL');
        $this->addSql('DROP INDEX idx_29a5ec27c9486a13 ON produit');
        $this->addSql('CREATE INDEX id_categorie ON produit (id_categorie)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27C9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie)');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6E1A2AFF1');
        $this->addSql('ALTER TABLE review CHANGE id_conseil id_conseil INT NOT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT rev_con FOREIGN KEY (id_conseil) REFERENCES conseil (id_conseil) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
