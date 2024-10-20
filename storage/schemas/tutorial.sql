CREATE DATABASE IF NOT EXISTS `poemdaily`;

CREATE TABLE `users`
(
    `id`    int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
    `name`  varchar(255) NOT NULL COMMENT 'User Name',
    `email` varchar(255) NOT NULL COMMENT 'User Email Address',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`name`, `email`)
VALUES ('Phalcon Team', 'team@phalcon.io');
