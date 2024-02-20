<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218164719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__prescription AS SELECT id, medicament, posologie, date_debut, date_fin, id_sejour_id FROM prescription');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('CREATE TABLE prescription (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, medicament VARCHAR(255) NOT NULL, posologie CLOB NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, id_sejour_id INTEGER NOT NULL, id_medecin_id INTEGER NOT NULL, CONSTRAINT FK_1FBFB8D988794CE8 FOREIGN KEY (id_sejour_id) REFERENCES sejour (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1FBFB8D9A1799A53 FOREIGN KEY (id_medecin_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO prescription (id, medicament, posologie, date_debut, date_fin, id_sejour_id, id_medecin_id) SELECT id, medicament, posologie, date_debut, date_fin, id_sejour_id, 32 FROM __temp__prescription');
        $this->addSql('DROP TABLE __temp__prescription');
        $this->addSql('CREATE INDEX IDX_1FBFB8D988794CE8 ON prescription (id_sejour_id)');
        $this->addSql('CREATE INDEX IDX_1FBFB8D9A1799A53 ON prescription (id_medecin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__prescription AS SELECT id, medicament, posologie, date_debut, date_fin, id_sejour_id FROM prescription');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('CREATE TABLE prescription (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, medicament VARCHAR(255) NOT NULL, posologie CLOB NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, id_sejour_id INTEGER NOT NULL, CONSTRAINT FK_1FBFB8D988794CE8 FOREIGN KEY (id_sejour_id) REFERENCES sejour (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO prescription (id, medicament, posologie, date_debut, date_fin, id_sejour_id) SELECT id, medicament, posologie, date_debut, date_fin, id_sejour_id FROM __temp__prescription');
        $this->addSql('DROP TABLE __temp__prescription');
        $this->addSql('CREATE INDEX IDX_1FBFB8D988794CE8 ON prescription (id_sejour_id)');
    }
}
