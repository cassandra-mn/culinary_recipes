<?php
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
Dotenv::createImmutable(__DIR__ . '/../..')->load();

$statement = <<<SQL
    SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
    SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
    SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

    CREATE TABLE IF NOT EXISTS `users` (
      `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '\n',
      `name` VARCHAR(100) NULL,
      `login` VARCHAR(100) NOT NULL,
      `password` VARCHAR(100) NOT NULL,
      `created_at` DATETIME NOT NULL,
      `updated_at` DATETIME NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE INDEX `login_UNIQUE` (`login` ASC))
    ENGINE = InnoDB;

    CREATE TABLE IF NOT EXISTS `categories` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(100) NULL,
      PRIMARY KEY (`id`),
      UNIQUE INDEX `name_UNIQUE` (`name` ASC))
    ENGINE = InnoDB;

    CREATE TABLE IF NOT EXISTS `recipes` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT(10) UNSIGNED NOT NULL,
      `category_id` INT(10) UNSIGNED NULL,
      `name` VARCHAR(45) NULL,
      `preparation_time` INT UNSIGNED NULL, -- em minutos
      `preparation_method` TEXT NOT NULL,
      `portions` INT UNSIGNED NULL,
      `ingredients` TEXT NULL,
      `created_at` DATETIME NOT NULL,
      `updated_at` DATETIME NOT NULL,
      PRIMARY KEY (`id`),
      INDEX `fk_recipes_1_idx` (`user_id` ASC),
      INDEX `fk_recipes_2_idx` (`category_id` ASC),
      CONSTRAINT `fk_recipes_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
      CONSTRAINT `fk_recipes_2`
        FOREIGN KEY (`category_id`)
        REFERENCES `categories` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
    ENGINE = InnoDB;

    CREATE TABLE IF NOT EXISTS `revoked_tokens` (
      `jti` VARCHAR(64) NOT NULL PRIMARY KEY,
      `expires_at` DATETIME NOT NULL,
      `created_at` DATETIME NOT NULL,
      INDEX `idx_revoked_expires` (`expires_at`)
    ) ENGINE = InnoDB;

    SET SQL_MODE=@OLD_SQL_MODE;
    SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
    SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

$categories = <<<SQL
    INSERT IGNORE INTO `categories` (`id`, `name`) VALUES
      (1, 'Bolos e tortas doces'),
      (2, 'Carnes'),
      (3, 'Aves'),
      (4, 'Peixes e frutos do mar'),
      (5, 'Saladas, molhos e acompanhamentos'),
      (6, 'Sopas'),
      (7, 'Massas'),
      (8, 'Bebidas'),
      (9, 'Doces e sobremesas'),
      (10, 'Lanches'),
      (11, 'Prato Único'),
      (12, 'Light'),
      (13, 'Alimentação Saudável');
SQL;

try {
    $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};charset=utf8mb4";
    $dbConnection = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $database = str_replace('`', '``', $_ENV['DB_DATABASE']);
    $dbConnection->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4");
    $dbConnection->exec("USE `{$database}`");
    $dbConnection->exec($statement);

    $dbConnection->beginTransaction();
    $dbConnection->exec($categories);
    $dbConnection->commit();

    echo 'Database e tabelas criados com sucesso.';
} catch (PDOException $e) {
    if (isset($dbConnection) && $dbConnection->inTransaction()) {
        $dbConnection->rollBack();
    }

    echo 'Erro ao criar database e tabelas: ' . $e->getMessage();
}
