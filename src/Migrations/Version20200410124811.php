<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410124811 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer_card (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id CHAR(13) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, game_id CHAR(13) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_98197A65E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_card (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, card_id INT NOT NULL, INDEX IDX_B40EC8E099E6F5DF (player_id), INDEX IDX_B40EC8E04ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_card (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, answer_count INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, game_id CHAR(13) NOT NULL, question_card_id INT DEFAULT NULL, winner_id INT DEFAULT NULL, INDEX IDX_C5EEEA34E48FD905 (game_id), INDEX IDX_C5EEEA3444104F3D (question_card_id), INDEX IDX_C5EEEA345DFCD4B8 (winner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round_card (id INT AUTO_INCREMENT NOT NULL, round_id INT NOT NULL, card_id INT NOT NULL, INDEX IDX_B3A38973A6005CA0 (round_id), INDEX IDX_B3A389734ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE player_card ADD CONSTRAINT FK_B40EC8E099E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player_card ADD CONSTRAINT FK_B40EC8E04ACC9A20 FOREIGN KEY (card_id) REFERENCES answer_card (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA3444104F3D FOREIGN KEY (question_card_id) REFERENCES question_card (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA345DFCD4B8 FOREIGN KEY (winner_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE round_card ADD CONSTRAINT FK_B3A38973A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE round_card ADD CONSTRAINT FK_B3A389734ACC9A20 FOREIGN KEY (card_id) REFERENCES answer_card (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE player_card DROP FOREIGN KEY FK_B40EC8E04ACC9A20');
        $this->addSql('ALTER TABLE round_card DROP FOREIGN KEY FK_B3A389734ACC9A20');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34E48FD905');
        $this->addSql('ALTER TABLE player_card DROP FOREIGN KEY FK_B40EC8E099E6F5DF');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA345DFCD4B8');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA3444104F3D');
        $this->addSql('ALTER TABLE round_card DROP FOREIGN KEY FK_B3A38973A6005CA0');
        $this->addSql('DROP TABLE answer_card');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE player_card');
        $this->addSql('DROP TABLE question_card');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE round_card');
    }
}
