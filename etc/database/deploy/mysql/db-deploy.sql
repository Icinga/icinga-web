-- Fragment begins: 1 --
INSERT INTO changelog (change_number, delta_set, start_dt, applied_by, description) VALUES (1, 'Main', NOW(), 'dbdeploy', '1-initial-scheme.sql');
-- //


CREATE TABLE `nsm_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_level` int(10) NOT NULL,
  `log_message` varchar(4000) NOT NULL,
  `log_created` datetime NOT NULL,
  `log_modified` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1097 DEFAULT CHARSET=latin1;

CREATE TABLE `nsm_role` (
  `role_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(40) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  `role_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `role_created` datetime NOT NULL,
  `role_modified` datetime NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `nsm_session` (
  `session_entry_id` int(10) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `session_data` longblob NOT NULL,
  `session_checksum` varchar(255) NOT NULL,
  `session_created` datetime NOT NULL,
  `session_modified` datetime NOT NULL,
  PRIMARY KEY (`session_entry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;

CREATE TABLE `nsm_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_account` int(40) NOT NULL,
  `user_name` varchar(18) NOT NULL,
  `user_lastname` varchar(40) NOT NULL,
  `user_firstname` varchar(40) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_salt` varchar(64) NOT NULL,
  `user_email` varchar(40) NOT NULL,
  `user_disabled` tinyint(1) NOT NULL DEFAULT '1',
  `user_created` datetime NOT NULL,
  `user_modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nsm_user_name_idx` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE `nsm_user_role` (
  `usro_user_id` int(10) NOT NULL,
  `usro_role_id` int(10) NOT NULL,
  PRIMARY KEY (`usro_user_id`,`usro_role_id`),
  KEY `nsm_usro_role_fk` (`usro_role_id`),
  CONSTRAINT `nsm_usro_role_fk` FOREIGN KEY (`usro_role_id`) REFERENCES `nsm_role` (`role_id`),
  CONSTRAINT `nsm_usro_user_fk` FOREIGN KEY (`usro_user_id`) REFERENCES `nsm_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `nsm_user_preference` (
  `upref_id` int(10) NOT NULL AUTO_INCREMENT,
  `upref_user_id` int(10) NOT NULL,
  `upref_val` varchar(100) DEFAULT NULL,
  `upref_longval` blob,
  `upref_key` varchar(50) NOT NULL,
  `upref_created` datetime NOT NULL,
  `upref_modified` datetime NOT NULL,
  PRIMARY KEY (`upref_id`),
  UNIQUE KEY `nsm_user_preference_userkey` (`upref_user_id`,`upref_key`),
  CONSTRAINT `nsm_user_nsm_user_preference_fk` FOREIGN KEY (`upref_user_id`) REFERENCES `nsm_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

UPDATE changelog SET complete_dt = NOW() WHERE change_number = 1 AND delta_set = 'Main';
-- Fragment ends: 1 --
-- Fragment begins: 2 --
INSERT INTO changelog (change_number, delta_set, start_dt, applied_by, description) VALUES (2, 'Main', NOW(), 'dbdeploy', '2-initial-data.sql');
-- //

INSERT INTO `nsm_user` VALUES
(1,0,'root','Root','Enoch','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','root@localhost.local',0,'2009-02-18 10:12:59','2009-02-18 10:12:59');

INSERT INTO `nsm_role` VALUES 
(3,'appkit_user','Appkit user',0,'2009-02-19 09:17:37','2009-04-20 16:12:17'),
(4,'appkit_admin','AppKit admin',0,'2009-02-17 10:17:10','0000-00-00 00:00:00'),(5,'icinga_user','The default representation of a ICINGA user',0,'2009-04-20 15:56:52','2009-04-20 15:56:52');

INSERT INTO `nsm_user_role` VALUES (1,4),(1,5);

UPDATE changelog SET complete_dt = NOW() WHERE change_number = 2 AND delta_set = 'Main';
-- Fragment ends: 2 --
-- Fragment begins: 3 --
INSERT INTO changelog (change_number, delta_set, start_dt, applied_by, description) VALUES (3, 'Main', NOW(), 'dbdeploy', '3-principal-scheme.sql');
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
    REFERENCES `icinga_web`.`nsm_principal` (`principal_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_nsm_principal_has_nsm_target_nsm_target1`
    FOREIGN KEY (`pt_target_id` )
    REFERENCES `icinga_web`.`nsm_target` (`target_id` )
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
  `target_name` VARCHAR(45) NOT NULL ,
  `target_description` VARCHAR(100) NULL DEFAULT NULL ,
  `target_class` VARCHAR(45) NOT NULL ,
  `target_type` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`target_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


UPDATE changelog SET complete_dt = NOW() WHERE change_number = 3 AND delta_set = 'Main';
-- Fragment ends: 3 --
-- Fragment begins: 4 --
INSERT INTO changelog (change_number, delta_set, start_dt, applied_by, description) VALUES (4, 'Main', NOW(), 'dbdeploy', '4-principal-data.sql');
-- //

INSERT INTO `nsm_principal` VALUES 
	(1,1,NULL,'user',0),
	(2,NULL,3,'role',0),
	(3,NULL,4,'role',0),
	(4,NULL,5,'role',0);

UPDATE changelog SET complete_dt = NOW() WHERE change_number = 4 AND delta_set = 'Main';
-- Fragment ends: 4 --
