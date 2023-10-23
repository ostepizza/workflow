-- Start the transaction
START TRANSACTION;

-- Create 'user'-table 
CREATE TABLE `user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `first_name` varchar(255) NOT NULL,
    `last_name` varchar(255) NOT NULL,
    `telephone` varchar(255) DEFAULT NULL,
    `location` varchar(255) DEFAULT NULL,
    `birthday` date DEFAULT NULL,
    `picture` longblob DEFAULT NULL,
    `cv` longblob DEFAULT NULL,
    `searchable` TINYINT(1) DEFAULT 0 NOT NULL,
    `competence` VARCHAR(5000) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `company` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(500) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `company_management` (
    `user_id` INT(11) NOT NULL ,
    `company_id` INT(11) NOT NULL ,
    `superuser` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`, `company_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (`company_id`) REFERENCES `company`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE = InnoDB;


-- Commit the changes
COMMIT;