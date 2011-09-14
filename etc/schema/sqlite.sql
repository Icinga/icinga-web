/*****************************************************/
/* Auto generated mysql SQL Schema file for icinga-web*/
/* Creation date: 2011-02-01T16:40:23+01:00          */
/****************************************************/


/*           SQL schema defintiion        */
CREATE TABLE nsm_db_version (vers_id INTEGER, version INT, PRIMARY KEY (vers_id));

CREATE TABLE nsm_log (
	log_id INTEGER PRIMARY KEY AUTOINCREMENT ,
	log_level INTEGER NOT NULL, 
	log_message TEXT NOT NULL, 
	log_created DATETIME NOT NULL, 
	log_modified DATETIME NOT NULL
);

CREATE TABLE nsm_principal (
	principal_id INTEGER PRIMARY KEY ,
	principal_user_id INTEGER, 
	principal_role_id INTEGER, 
	principal_type, 
	principal_disabled INTEGER DEFAULT '0', 
	FOREIGN KEY (principal_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE, 
	FOREIGN KEY (principal_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX principal_user_id_idx ON nsm_principal(principal_user_id);
CREATE INDEX principal_role_id_idx ON nsm_principal(principal_role_id);

CREATE TABLE nsm_principal_target (
	pt_id INTEGER PRIMARY KEY AUTOINCREMENT , 
	pt_principal_id INTEGER NOT NULL, 
	pt_target_id INTEGER NOT NULL,
	FOREIGN KEY (pt_target_id) REFERENCES nsm_target(target_id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (pt_principal_id) REFERENCES nsm_principal(principal_id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX pt_target_id_ix_idx ON nsm_principal_target(pt_target_id);
CREATE INDEX pt_principal_id_ix_idx ON nsm_principal_target(pt_principal_id);

CREATE TABLE nsm_role (
	role_id INTEGER PRIMARY KEY AUTOINCREMENT, 
	role_name VARCHAR(40) NOT NULL,
	role_description VARCHAR(255),
	role_disabled INTEGER DEFAULT '0' NOT NULL, 
	role_created DATETIME NOT NULL, 
	role_modified DATETIME NOT NULL, 
	role_parent INTEGER, 
	FOREIGN KEY (role_parent) REFERENCES nsm_role(role_id)
);

CREATE INDEX role_parent_idx ON nsm_role(role_parent);

CREATE TABLE nsm_session (
	session_entry_id INTEGER PRIMARY KEY AUTOINCREMENT , 
	session_id VARCHAR(255) NOT NULL, 
	session_name VARCHAR(255) NOT NULL, 
	session_data LONGTEXT NOT NULL, 
	session_checksum VARCHAR(255) NOT NULL, 
	session_created DATETIME NOT NULL, 
	session_modified DATETIME NOT NULL
);

CREATE TABLE nsm_target (
	target_id INTEGER PRIMARY KEY AUTOINCREMENT , 
	target_name VARCHAR(45) NOT NULL, 
	target_description VARCHAR(100), 
	target_class VARCHAR(80), 
	target_type VARCHAR(45) NOT NULL
);

CREATE TABLE nsm_target_value (
	tv_pt_id INTEGER PRIMARY KEY AUTOINCREMENT , 
	tv_key VARCHAR(45), 
	tv_val VARCHAR(45) NOT NULL, 
	FOREIGN KEY (tv_pt_id) REFERENCES nsm_principal_target(pt_id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE nsm_user (
	user_id INTEGER PRIMARY KEY AUTOINCREMENT,
	user_account INTEGER DEFAULT 0 NOT NULL, 
	user_name VARCHAR(127) NOT NULL, 
	user_lastname VARCHAR(40) NOT NULL, 
	user_firstname VARCHAR(40) NOT NULL, 
	user_password VARCHAR(64) NOT NULL, 
	user_salt VARCHAR(64) NOT NULL, 
	user_authsrc VARCHAR(45) DEFAULT 'internal' NOT NULL, 
	user_authid VARCHAR(127), 
	user_authkey VARCHAR(64), 
	user_email VARCHAR(254) NOT NULL, 
	user_disabled INTEGER DEFAULT '1' NOT NULL, 
	user_created DATETIME NOT NULL, 
	user_modified DATETIME NOT NULL
);
CREATE UNIQUE INDEX user_name_unique_idx ON nsm_user(user_name);
CREATE UNIQUE INDEX user_email_unique_idx ON nsm_user(user_email);
CREATE INDEX user_search_idx ON nsm_user(user_name, user_authsrc, user_authid, user_disabled);

CREATE TABLE nsm_user_preference (
	upref_id INTEGER PRIMARY KEY AUTOINCREMENT,
	upref_user_id INTEGER NOT NULL, 
	upref_val VARCHAR(100), 
	upref_longval LONGTEXT, 
	upref_key VARCHAR(50) NOT NULL, 
	upref_created DATETIME NOT NULL, 
	upref_modified DATETIME NOT NULL,
	FOREIGN KEY (upref_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE
);
CREATE INDEX upref_search_key_idx_idx ON nsm_user_preference(upref_key);
CREATE INDEX principal_role_id_ix_idx ON nsm_user_preference(upref_user_id);

CREATE TABLE nsm_user_role (
	usro_user_id INTEGER, 
	usro_role_id INTEGER,
	FOREIGN KEY (usro_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (usro_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE,
	PRIMARY KEY (usro_user_id, usro_role_id) 
);

CREATE TABLE cronk (
	cronk_id INTEGER PRIMARY KEY AUTOINCREMENT, 
	cronk_uid VARCHAR(45) UNIQUE, 
	cronk_name VARCHAR(45), 
	cronk_description VARCHAR(100), 
	cronk_xml LONGTEXT, 
	cronk_user_id INTEGER, 
	cronk_created DATETIME NOT NULL, 
	cronk_modified DATETIME NOT NULL
);


CREATE TABLE cronk_category (
	cc_id INTEGER PRIMARY KEY AUTOINCREMENT, 
	cc_uid VARCHAR(45) NOT NULL UNIQUE, 
	cc_name VARCHAR(45), 
	cc_visible TINYINT DEFAULT '0', 
	cc_position INT DEFAULT '0', 
	cc_created DATETIME NOT NULL, 
	cc_modified DATETIME NOT NULL
);

CREATE TABLE cronk_category_cronk (
	ccc_cc_id INTEGER,
	ccc_cronk_id INTEGER,
	PRIMARY KEY(ccc_cc_id, ccc_cronk_id)
);

CREATE TABLE cronk_principal_cronk (
	cpc_principal_id INTEGER, 
	cpc_cronk_id INTEGER, 
	PRIMARY KEY(cpc_principal_id, cpc_cronk_id)
);


/*          Initial data import              */
 
INSERT INTO nsm_db_version (vers_id,version) VALUES ('1','2');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('1','IcingaHostgroup','Limit data access to specific hostgroups','IcingaDataHostgroupPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('2','IcingaServicegroup','Limit data access to specific servicegroups','IcingaDataServicegroupPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('3','IcingaHostCustomVariablePair','Limit data access to specific custom variables','IcingaDataHostCustomVariablePrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('4','IcingaServiceCustomVariablePair','Limit data access to specific custom variables','IcingaDataServiceCustomVariablePrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('5','IcingaContactgroup','Limit data access to users contact group membership','IcingaDataContactgroupPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('6','IcingaCommandRo','Limit access to commands','IcingaDataCommandRoPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('7','appkit.access','Access to login-page (which, actually, means no access)','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('8','icinga.user','Access to icinga','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('9','appkit.admin.groups','Access to group editor','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('10','appkit.admin.users','Access to user editor','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('11','appkit.admin','Access to admin panel ','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('12','appkit.user.dummy','Basic right for users','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('13','appkit.api.access','Access to web-based api adapter','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('14','icinga.demoMode','Hide features like password reset which are not wanted in demo systems','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('15','icinga.cronk.category.admin','Enables category admin features','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('16','icinga.cronk.log','Allow user to view icinga-log','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('17','icinga.control.view','Allow user to view icinga status','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('18','icinga.control.admin','Allow user to administrate the icinga process','','credential');

INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_modified,role_created) VALUES ('1','icinga_user','The default representation of an icinga user','0',date('now'),date('now'));
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_modified,role_created) VALUES ('2','appkit_user','Appkit user test','0',date('now'),date('now'));
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_parent,role_modified,role_created) VALUES ('3','appkit_admin','AppKit admin','0','2',date('now'),date('now'));
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_modified,role_created) VALUES ('4','guest','Unauthorized Guest','0',date('now'),date('now'));

INSERT INTO nsm_user (user_id,user_account,user_name,user_firstname,user_lastname,user_password,user_salt,user_authsrc,user_email,user_disabled,user_modified,user_created) VALUES ('1','0','root','Enoch','Root','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','internal','root@localhost.local','0',date('now'),date('now'));

INSERT INTO nsm_principal (principal_id,principal_user_id,principal_type,principal_disabled) VALUES ('1','1','user','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('2','2','role','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('3','3','role','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('4','1','role','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('5','4','role','0');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('1','2','8');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('2','2','13');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('3','3','9');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('4','3','10');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('5','3','11');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('6','4','8');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('7','5','7');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('8','3','15');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('9','3','16');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('10','3','17');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('11','3','18');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','1');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','2');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','3');

