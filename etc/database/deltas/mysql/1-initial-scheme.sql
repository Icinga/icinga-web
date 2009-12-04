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

-- //@UNDO

-- //