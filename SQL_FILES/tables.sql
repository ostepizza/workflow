-- Start the transaction
START TRANSACTION;

-- Create 'user'-table 
CREATE TABLE `user` (
    `id` int(11) NOT NULL,
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
    `competence` VARCHAR(5000) DEFAULT NULL;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add primary key 'id' to table 'user'
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`);

-- Make column 'id' in table 'user' auto-increment
ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- Commit the changes
COMMIT;