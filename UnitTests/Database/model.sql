SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `countries`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `countries` ;

CREATE  TABLE IF NOT EXISTS `countries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `UQ_countries__name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cities` ;

CREATE  TABLE IF NOT EXISTS `cities` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `country` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `postal_code` SMALLINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `FK_cities__country` (`country` ASC) ,
  UNIQUE INDEX `UQ_cities__contry__postal_code` (`country` ASC, `postal_code` ASC) ,
  CONSTRAINT `FK_cities__country`
    FOREIGN KEY (`country` )
    REFERENCES `countries` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `people`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `people` ;

CREATE  TABLE IF NOT EXISTS `people` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(255) NOT NULL ,
  `last_name` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NULL ,
  `address` VARCHAR(255) NOT NULL ,
  `city` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `FK_people__city` (`city` ASC) ,
  UNIQUE INDEX `UQ_people__email` (`email` ASC) ,
  CONSTRAINT `FK_people__city`
    FOREIGN KEY (`city` )
    REFERENCES `cities` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users` ;

CREATE  TABLE IF NOT EXISTS `users` (
  `person` INT UNSIGNED NOT NULL ,
  `nickname` VARCHAR(16) NOT NULL ,
  `password` CHAR(40) NOT NULL ,
  `registered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `last_login` TIMESTAMP NULL ,
  PRIMARY KEY (`person`) ,
  INDEX `FK_users__person` (`person` ASC) ,
  CONSTRAINT `FK_users__person`
    FOREIGN KEY (`person` )
    REFERENCES `people` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `countries`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO countries (`id`, `name`) VALUES (1, 'Denmark');
INSERT INTO countries (`id`, `name`) VALUES (2, 'Norway');
INSERT INTO countries (`id`, `name`) VALUES (3, 'Sweden');
INSERT INTO countries (`id`, `name`) VALUES (4, 'Finland');
INSERT INTO countries (`id`, `name`) VALUES (5, 'Iceland');
INSERT INTO countries (`id`, `name`) VALUES (6, 'Germany');

COMMIT;

-- -----------------------------------------------------
-- Data for table `cities`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO cities (`id`, `country`, `name`, `postal_code`) VALUES (1, 1, 'Århus', 8000);
INSERT INTO cities (`id`, `country`, `name`, `postal_code`) VALUES (2, 1, 'København', 1000);
INSERT INTO cities (`id`, `country`, `name`, `postal_code`) VALUES (3, 6, 'Hamburg', 20253);

COMMIT;

-- -----------------------------------------------------
-- Data for table `people`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO people (`id`, `first_name`, `last_name`, `email`, `address`, `city`) VALUES (1, 'Anders', 'Ingemann', 'anders@ingemann.de', 'Vej 13', 1);

COMMIT