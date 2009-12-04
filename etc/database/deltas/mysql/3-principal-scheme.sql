-- //

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE TABLE `nsm_principal_target` (
  `pt_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `pt_principal_id` INT(11) NOT NULL ,
  `pt_target_id` INT(11) NOT NULL ,
  INDEX `fk_nsm_principal_has_nsm_target_nsm_principal1` (`pt_principal_id` ASC) ,
  INDEX `fk_nsm_principal_has_nsm_target_nsm_target1` (`pt_target_id` ASC) ,
  PRIMARY KEY (`pt_id`) ,
  CONSTRAINT `fk_nsm_principal_has_nsm_target_nsm_principal1`
    FOREIGN KEY (`pt_principal_id` )
    REFERENCES `nsm_principal` (`principal_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_nsm_principal_has_nsm_target_nsm_target1`
    FOREIGN KEY (`pt_target_id` )
    REFERENCES `nsm_target` (`target_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

CREATE TABLE `nsm_principal` (
  `principal_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `principal_user_id` INT(10) NULL DEFAULT NULL ,
  `principal_role_id` INT(10) NULL DEFAULT NULL ,
  `principal_type` ENUM('role', 'user') NOT NULL ,
  `principal_disabled` TINYINT(4) NULL DEFAULT 0 ,
  PRIMARY KEY (`principal_id`) ,
  INDEX `fk_nsm_principle_nsm_user1` (`principal_user_id` ASC) ,
  INDEX `fk_nsm_principle_nsm_role1` (`principal_role_id` ASC) ,
  CONSTRAINT `fk_nsm_principle_nsm_user1`
    FOREIGN KEY (`principal_user_id` )
    REFERENCES `nsm_user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_nsm_principle_nsm_role1`
    FOREIGN KEY (`principal_role_id` )
    REFERENCES `nsm_role` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

CREATE TABLE `nsm_target_value` (
  `tv_pt_id` INT(11) NOT NULL ,
  `tv_key` VARCHAR(45) NOT NULL ,
  `tv_val` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`tv_pt_id`, `tv_key`) ,
  INDEX `fk_nsm_target_value_nsm_principal_target1` (`tv_pt_id` ASC) ,
  CONSTRAINT `fk_nsm_target_value_nsm_principal_target1`
    FOREIGN KEY (`tv_pt_id` )
    REFERENCES `nsm_principal_target` (`pt_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

CREATE TABLE `nsm_target` (
  `target_id` INT(11) NOT NULL ,
  `target_name` VARCHAR(45) NULL DEFAULT NULL ,
  `target_description` VARCHAR(100) NULL DEFAULT NULL ,
  `target_class` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`target_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


-- //@UNDO

-- //