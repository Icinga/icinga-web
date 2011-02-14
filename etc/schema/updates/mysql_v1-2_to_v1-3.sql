CREATE TABLE `cronk` (
  `cronk_id` int(11) NOT NULL AUTO_INCREMENT,
  `cronk_uid` varchar(45) DEFAULT NULL,
  `cronk_name` varchar(45) DEFAULT NULL,
  `cronk_description` varchar(100) DEFAULT NULL,
  `cronk_xml` text,
  `cronk_user_id` int(11) DEFAULT NULL,
  `cronk_created` datetime NOT NULL,
  `cronk_modified` datetime NOT NULL,
  PRIMARY KEY (`cronk_id`),
  UNIQUE KEY `cronk_uid_UNIQUE_idx` (`cronk_uid`),
  KEY `cronk_user_id_idx` (`cronk_user_id`),
  CONSTRAINT `cronk_cronk_user_id_nsm_user_user_id` FOREIGN KEY (`cronk_user_id`) REFERENCES `nsm_user` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `cronk_category` (
  `cc_id` int(11) NOT NULL AUTO_INCREMENT,
  `cc_uid` varchar(45) NOT NULL,
  `cc_name` varchar(45) DEFAULT NULL,
  `cc_visible` tinyint(4) DEFAULT '0',
  `cc_position` int(11) DEFAULT '0',
  `cc_created` datetime NOT NULL,
  `cc_modified` datetime NOT NULL,
  PRIMARY KEY (`cc_id`),
  UNIQUE KEY `cc_uid_UNIQUE_idx` (`cc_uid`)
) ENGINE=InnoDB;

CREATE TABLE `cronk_category_cronk` (
  `ccc_cc_id` int(11) NOT NULL DEFAULT '0',
  `ccc_cronk_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccc_cc_id`,`ccc_cronk_id`),
  KEY `cronk_category_cronk_ccc_cronk_id_cronk_cronk_id` (`ccc_cronk_id`),
  CONSTRAINT `cronk_category_cronk_ccc_cc_id_cronk_category_cc_id` FOREIGN KEY (`ccc_cc_id`) REFERENCES `cronk_category` (`cc_id`),
  CONSTRAINT `cronk_category_cronk_ccc_cronk_id_cronk_cronk_id` FOREIGN KEY (`ccc_cronk_id`) REFERENCES `cronk` (`cronk_id`)
) ENGINE=InnoDB;

CREATE TABLE `cronk_principal_cronk` (
  `cpc_principal_id` int(11) NOT NULL DEFAULT '0',
  `cpc_cronk_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cpc_principal_id`,`cpc_cronk_id`),
  KEY `cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id` (`cpc_cronk_id`),
  CONSTRAINT `ccnp` FOREIGN KEY (`cpc_principal_id`) REFERENCES `nsm_principal` (`principal_id`),
  CONSTRAINT `cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id` FOREIGN KEY (`cpc_cronk_id`) REFERENCES `cronk` (`cronk_id`)
) ENGINE=InnoDB;

-- Adding new credential and add them to appkit_admin

INSERT INTO nsm_target (target_name,target_description,target_class,target_type) VALUES ('icinga.control.view','Allow user to view icinga status','','credential');
INSERT INTO nsm_target (target_name,target_description,target_class,target_type) VALUES ('icinga.control.admin','Allow user to administrate the icinga process','','credential');
INSERT INTO nsm_target (`target_name`, `target_description`, `target_type`) VALUES ('icinga.cronk.category.admin', 'Enables category admin feature', 'credential');
SET @TARGET_ID=LAST_INSERT_ID();
INSERT INTO nsm_principal_target (`pt_principal_id`, `pt_target_id`) VALUES ('3', @TARGET_ID);
