<?php
require 'bootstrap.php';

$statement = <<<SQL
    SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
    SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
    SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';
    
    CREATE SCHEMA IF NOT EXISTS `culinary_recipes` ;
    USE `culinary_recipes` ;
    
    CREATE TABLE IF NOT EXISTS `culinary_recipes`.`users` (
      `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '\n',
      `name` VARCHAR(100) NULL,
      `login` VARCHAR(100) NOT NULL,
      `password` VARCHAR(100) NOT NULL,
      `created_at` DATETIME NOT NULL,
      `updated_at` DATETIME NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE INDEX `login_UNIQUE` (`login` ASC))
    ENGINE = InnoDB;
    
    CREATE TABLE IF NOT EXISTS `culinary_recipes`.`categories` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(100) NULL,
      PRIMARY KEY (`id`),
      UNIQUE INDEX `name_UNIQUE` (`name` ASC))
    ENGINE = InnoDB;
    
    CREATE TABLE IF NOT EXISTS `culinary_recipes`.`recipes` (
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
        REFERENCES `culinary_recipes`.`users` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
      CONSTRAINT `fk_recipes_2`
        FOREIGN KEY (`category_id`)
        REFERENCES `culinary_recipes`.`categories` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
    ENGINE = InnoDB;

    SET SQL_MODE=@OLD_SQL_MODE;
    SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
    SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
    
    START TRANSACTION;
    USE `culinary_recipes`;
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (1, 'Bolos e tortas doces');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (2, 'Carnes');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (3, 'Aves');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (4, 'Peixes e frutos do mar');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (5, 'Saladas, molhos e acompanhamentos');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (6, 'Sopas');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (7, 'Massas');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (8, 'Bebidas');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (9, 'Doces e sobremesas');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (10, 'Lanches');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (11, 'Prato Único');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (12, 'Light');
    INSERT INTO `culinary_recipes`.`categories` (`id`, `name`) VALUES (13, 'Alimentação Saudável');
    
    COMMIT;
SQL;

try {
    $dbConnection->exec($statement);
    echo "Database and tables created successfully.";
} catch (PDOException $e) {
    echo "Error creating Database and tables: " . $e->getMessage();
}
