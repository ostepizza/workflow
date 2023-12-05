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
    `picture` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_category` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_listing` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) DEFAULT NULL,
    `description` VARCHAR(5000) DEFAULT NULL,
    `deadline` date DEFAULT NULL,
    `published` TINYINT(1) DEFAULT 0 NOT NULL,
    `views` int(11) DEFAULT 0 NOT NULL,
    `company_id` int(11) NOT NULL,
    `job_category_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`company_id`) REFERENCES `company`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (`job_category_id`) REFERENCES `job_category`(`id`)
        ON DELETE SET NULL
        ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_application` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) DEFAULT NULL,
    `text` VARCHAR(5000) DEFAULT NULL,
    `sent` TINYINT(1) DEFAULT 0 NOT NULL,
    `sent_datetime` datetime DEFAULT NULL,
    `pinned` TINYINT(1) DEFAULT 0 NOT NULL,
    `archived` TINYINT(1) DEFAULT 0 NOT NULL,
    `job_listing_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`job_listing_id`) REFERENCES `job_listing`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Some test data
INSERT INTO `user` (`id`, `email`, `password`, `first_name`, `last_name`, `telephone`, `location`, `birthday`, `picture`, `cv`, `searchable`, `competence`) VALUES (NULL, 'test@test.net', 'kjhvdf', 'Test', 'User', NULL, NULL, NULL, NULL, NULL, '0', NULL), (NULL, 'jeff@jefferson.net', 'ojhdbf', 'Jeff', 'Jefferson', NULL, NULL, NULL, NULL, NULL, '0', NULL);
INSERT INTO `user` (`id`, `email`, `password`, `first_name`, `last_name`, `telephone`, `location`, `birthday`, `picture`, `cv`, `searchable`, `competence`) VALUES (NULL, 'bohan@smohan.net', 'ouishdg', 'Bohan', 'Smohan', NULL, NULL, NULL, NULL, NULL, '0', NULL), (NULL, 'ligmerglue@ieat.net', 'ksjhgfv', 'Ligmer', 'Glue', NULL, NULL, NULL, NULL, NULL, '0', NULL);
INSERT INTO `company` (`id`, `name`, `description`) VALUES (NULL, 'The Testing Company', 'We test things'), (NULL, 'The Pizza Shop', 'We sell pizzas!');

-- Commit the changes
COMMIT;