SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `tbl_employee` (
  `id` INT (11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','user') NOT NULL,
  `id_position` INT(11) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
CREATE TRIGGER `tgr_delete_user` BEFORE DELETE ON `tbl_employee` FOR EACH ROW DELETE FROM tbl_users
WHERE username = OLD.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tgr_insert_user` AFTER INSERT ON `tbl_employee` FOR EACH ROW INSERT INTO tbl_users(username, password, role)
	VALUES(
        NEW.username,
        NEW.password,
        NEW.role
        )
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tgr_update_user` AFTER UPDATE ON `tbl_employee` FOR EACH ROW UPDATE tbl_users
SET
	username = NEW.username,
    password = NEW.password,
    role = NEW.role
WHERE username = OLD.username
$$
DELIMITER ;

CREATE TABLE `password_resets` (
  `username` varchar(50) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_position` (
  `id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_position` (`id`, `position`) VALUES
(1, 'Admin');

CREATE TABLE `tbl_users` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `tbl_employee`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tbl_employee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_position` (`id_position`);

ALTER TABLE `tbl_position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `tbl_employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;