CREATE TABLE `symfony`.`news` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `excerpt` VARCHAR(255) NOT NULL,
  `full_text` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `created_published` (`created_at` ASC, `is_published` ASC));
