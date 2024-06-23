CREATE DATABASE IF NOT EXISTS `mimi-frame` CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;

USE `mimi-frame`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255),
    `active` TINYINT(1) NOT NULL DEFAULT '1',
    `password` VARCHAR(255) NOT NULL,
    primary key(id)
);

CREATE TABLE IF NOT EXISTS `mimi_systems` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT '1',
    `password` VARCHAR(255) NOT NULL,
    primary key(id)
);

CREATE TABLE IF NOT EXISTS `refresh_tokens_crm` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `token` VARCHAR(255) NOT NULL,
    `expire` BIGINT NOT NULL,
    primary key(id)
);

CREATE TABLE IF NOT EXISTS `jwt_tokens_crm` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `token` VARCHAR(255) NOT NULL,
    `expire` BIGINT NOT NULL,
    primary key(id)
);