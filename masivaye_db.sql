-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `simple_ride`.`user_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `simple_ride`.`user_data` (
  `user_id` INT(12) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `phone_num` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `profile_image` VARCHAR(255) NULL,
  `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `latitude` DOUBLE NULL,
  `longitude` DOUBLE NULL,
  `location_update_time` DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT '\n',
  `rating` INT(1) NULL,
  PRIMARY KEY (`user_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `simple_ride`.`driver_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `simple_ride`.`driver_data` (
  `driver_id` INT(12) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `identity_number` INT(12) NOT NULL,
  `phone_num` VARCHAR(45) NOT NULL,
  `profile_image` VARCHAR(255) NULL,
  `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `password` VARCHAR(45) NOT NULL,
  `latitude` DOUBLE NULL,
  `longitude` DOUBLE NULL,
  `location_update_time` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `work_status` DOUBLE NOT NULL,
  `rating` INT(1) NULL,
  PRIMARY KEY (`driver_id`),
  UNIQUE INDEX `identity_number_UNIQUE` (`identity_number` ASC),
  UNIQUE INDEX `phone_num_UNIQUE` (`phone_num` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `simple_ride`.`vehicle_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `simple_ride`.`vehicle_data` (
  `vehicle_id` INT(12) NOT NULL AUTO_INCREMENT,
  `vehicle_model` VARCHAR(45) NOT NULL,
  `vehicle_num_plate` VARCHAR(45) NOT NULL,
  `vehicle_profile_pic` VARCHAR(255) NOT NULL,
  `number_of_seats` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`vehicle_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `simple_ride`.`driver_data_has_vehicle_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `simple_ride`.`driver_data_has_vehicle_data` (
  `driver_data_driver_id` INT(12) NOT NULL,
  `vehicle_data_vehicle_id` INT(12) NOT NULL,
  PRIMARY KEY (`driver_data_driver_id`, `vehicle_data_vehicle_id`),
  INDEX `fk_driver_data_has_vehicle_data_vehicle_data1_idx` (`vehicle_data_vehicle_id` ASC),
  INDEX `fk_driver_data_has_vehicle_data_driver_data_idx` (`driver_data_driver_id` ASC),
  CONSTRAINT `fk_driver_data_has_vehicle_data_driver_data`
    FOREIGN KEY (`driver_data_driver_id`)
    REFERENCES `simple_ride`.`driver_data` (`driver_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_driver_data_has_vehicle_data_vehicle_data1`
    FOREIGN KEY (`vehicle_data_vehicle_id`)
    REFERENCES `simple_ride`.`vehicle_data` (`vehicle_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `simple_ride`.`on_going_requests`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `simple_ride`.`on_going_requests` (
  `id` INT(12) NOT NULL AUTO_INCREMENT,
  `source_latitude` DOUBLE NULL,
  `source_longitude` DOUBLE NULL,
  `destination_latitude` DOUBLE NULL,
  `destination_longitude` DOUBLE NULL,
  `current_latitude` DOUBLE NULL,
  `current_longitude` DOUBLE NULL,
  `user_data_user_id` INT(12) NOT NULL,
  `driver_data_driver_id` INT(12) NOT NULL,
  `destination_name` VARCHAR(45) NULL,
  `user_payment_method` VARCHAR(45) NULL,
  `request_status` VARCHAR(45) NULL,
  `distance_km` DOUBLE NULL DEFAULT 0,
  `quantity` INT(12) NULL DEFAULT 0,
  `rating_from_user` INT(1) NULL DEFAULT 0,
  `rating_from_driver` INT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`, `user_data_user_id`, `driver_data_driver_id`),
  INDEX `fk_on_going_requests_user_data1_idx` (`user_data_user_id` ASC),
  INDEX `fk_on_going_requests_driver_data1_idx` (`driver_data_driver_id` ASC),
  CONSTRAINT `fk_on_going_requests_user_data1`
    FOREIGN KEY (`user_data_user_id`)
    REFERENCES `simple_ride`.`user_data` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_on_going_requests_driver_data1`
    FOREIGN KEY (`driver_data_driver_id`)
    REFERENCES `simple_ride`.`driver_data` (`driver_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `simple_ride`.`confirmations_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `simple_ride`.`confirmations_data` (
  `id` INT(12) NOT NULL AUTO_INCREMENT,
  `phone` VARCHAR(45) NOT NULL,
  `confirmation_code` VARCHAR(45) NOT NULL,
  `request_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
