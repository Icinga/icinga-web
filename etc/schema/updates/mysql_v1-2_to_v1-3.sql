SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `cronk_category_cronk` (
  `ccc_cc_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ccc_cronk_id` INT(11) NOT NULL ,
  PRIMARY KEY (`ccc_cc_id`, `ccc_cronk_id`) ,
  INDEX `fk_cronk_category_has_cronk_cronk1` (`ccc_cronk_id` ASC) ,
  CONSTRAINT `fk_cronk_category_has_cronk_cronk_category1`
    FOREIGN KEY (`ccc_cc_id` )
    REFERENCES `cronk_category` (`cc_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cronk_category_has_cronk_cronk1`
    FOREIGN KEY (`ccc_cronk_id` )
    REFERENCES `cronk` (`cronk_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `cronk_category` (
  `cc_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cc_name` VARCHAR(45) NULL DEFAULT NULL ,
  `cc_visible` TINYINT(4) NULL DEFAULT 0 ,
  `cc_position` INT(11) NULL DEFAULT 0 ,
  `cc_created` DATETIME NULL DEFAULT NULL ,
  `cc_modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`cc_id`) ,
  UNIQUE INDEX `cc_name_UNIQUE` (`cc_name` ASC) )
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `cronk` (
  `cronk_id` INT(11) NOT NULL ,
  `cronk_uid` VARCHAR(45) NULL DEFAULT NULL ,
  `cronk_name` VARCHAR(45) NULL DEFAULT NULL ,
  `cronk_description` VARCHAR(100) NULL DEFAULT NULL ,
  `cronk_xml` TEXT NULL DEFAULT NULL ,
  `cronk_created` DATETIME NULL DEFAULT NULL  AFTER `cronk_xml` , 
  `cronk_modified` VARCHAR(45) NULL DEFAULT NULL  AFTER `cronk_created`,
  PRIMARY KEY (`cronk_id`) ,
  UNIQUE INDEX `cronk_uid_UNIQUE` (`cronk_uid` ASC) )
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `cronk_principal_cronk` (
  `cpc_principal_id` INT(11) NOT NULL ,
  `cpc_cronk_id` INT(11) NOT NULL ,
  PRIMARY KEY (`cpc_principal_id`, `cpc_cronk_id`) ,
  INDEX `fk_nsm_principal_has_cronk_cronk1` (`cpc_cronk_id` ASC) ,
  CONSTRAINT `fk_nsm_principal_has_cronk_nsm_principal1`
    FOREIGN KEY (`cpc_principal_id` )
    REFERENCES `nsm_principal` (`principal_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_nsm_principal_has_cronk_cronk1`
    FOREIGN KEY (`cpc_cronk_id` )
    REFERENCES `cronk` (`cronk_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

