-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema xss_catcher
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `xss_catcher` ;

-- -----------------------------------------------------
-- Schema xss_catcher
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `xss_catcher` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `xss_catcher` ;

-- -----------------------------------------------------
-- Table `xss_catcher`.`data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `xss_catcher`.`data` ;

CREATE TABLE IF NOT EXISTS `xss_catcher`.`data` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `method` VARCHAR(10) NULL,
  `headers` VARCHAR(6144) NULL,
  `data` VARCHAR(6144) NULL,
  `date` DATETIME NULL,
  `ip` VARCHAR(40) NULL,
  `site` VARCHAR(2048) NULL,
  `redirect` VARCHAR(2048) NULL,
  `info` VARCHAR(2048) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SET SQL_MODE = '';
DROP USER IF EXISTS xss_admin@127.0.0.1;
SET SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
CREATE USER 'xss_admin'@'127.0.0.1' IDENTIFIED BY 'xsscatcher';

GRANT ALL ON `xss_catcher`.* TO 'xss_admin'@'127.0.0.1';
SET SQL_MODE = '';
DROP USER IF EXISTS xss_admin@localhost;
SET SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
CREATE USER 'xss_admin'@'localhost' IDENTIFIED BY 'xsscatcher';

GRANT ALL ON `xss_catcher`.* TO 'xss_admin'@'localhost';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
