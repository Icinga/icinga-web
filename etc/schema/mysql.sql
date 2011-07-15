/*****************************************************/
/* Auto generated mysql SQL Schema file for icinga-web*/
/* Creation date: 2011-02-01T16:40:23+01:00          */
/****************************************************/


/*           SQL schema defintiion        */
CREATE TABLE cronk (cronk_id INT AUTO_INCREMENT, cronk_uid VARCHAR(45), cronk_name VARCHAR(45), cronk_description VARCHAR(100), cronk_xml TEXT, cronk_user_id INT, cronk_created DATETIME NOT NULL, cronk_modified DATETIME NOT NULL, UNIQUE INDEX cronk_uid_UNIQUE_idx (cronk_uid), INDEX cronk_user_id_idx (cronk_user_id), PRIMARY KEY(cronk_id)) ENGINE = INNODB;
CREATE TABLE cronk_category (cc_id INT AUTO_INCREMENT, cc_uid VARCHAR(45) NOT NULL, cc_name VARCHAR(45), cc_visible TINYINT DEFAULT '0', cc_position INT DEFAULT '0', cc_created DATETIME NOT NULL, cc_modified DATETIME NOT NULL, UNIQUE INDEX cc_uid_UNIQUE_idx (cc_uid), PRIMARY KEY(cc_id)) ENGINE = INNODB;
CREATE TABLE cronk_category_cronk (ccc_cc_id INT, ccc_cronk_id INT, PRIMARY KEY(ccc_cc_id, ccc_cronk_id)) ENGINE = INNODB;
CREATE TABLE cronk_principal_cronk (cpc_principal_id INT, cpc_cronk_id INT, PRIMARY KEY(cpc_principal_id, cpc_cronk_id)) ENGINE = INNODB;
CREATE TABLE nsm_db_version (vers_id INT, version INT, PRIMARY KEY(vers_id)) ENGINE = INNODB;
CREATE TABLE nsm_log (log_id INT AUTO_INCREMENT, log_level INT NOT NULL, log_message TEXT NOT NULL, log_created DATETIME NOT NULL, log_modified DATETIME NOT NULL, PRIMARY KEY(log_id)) ENGINE = INNODB;
CREATE TABLE nsm_principal (principal_id INT AUTO_INCREMENT, principal_user_id INT, principal_role_id INT, principal_type VARCHAR(4) NOT NULL, principal_disabled TINYINT DEFAULT '0', INDEX principal_user_id_idx (principal_user_id), INDEX principal_role_id_idx (principal_role_id), PRIMARY KEY(principal_id)) ENGINE = INNODB;
CREATE TABLE nsm_principal_target (pt_id INT AUTO_INCREMENT, pt_principal_id INT NOT NULL, pt_target_id INT NOT NULL, INDEX pt_target_id_ix_idx (pt_target_id), INDEX pt_principal_id_ix_idx (pt_principal_id), PRIMARY KEY(pt_id)) ENGINE = INNODB;
CREATE TABLE nsm_role (role_id INT AUTO_INCREMENT, role_name VARCHAR(40) NOT NULL, role_description VARCHAR(255), role_disabled TINYINT DEFAULT '0' NOT NULL, role_created DATETIME NOT NULL, role_modified DATETIME NOT NULL, role_parent INT, INDEX role_parent_idx (role_parent), PRIMARY KEY(role_id)) ENGINE = INNODB;
CREATE TABLE nsm_session (session_entry_id INT AUTO_INCREMENT, session_id VARCHAR(255) NOT NULL, session_name VARCHAR(255) NOT NULL, session_data LONGTEXT NOT NULL, session_checksum VARCHAR(255) NOT NULL, session_created DATETIME NOT NULL, session_modified DATETIME NOT NULL, PRIMARY KEY(session_entry_id)) ENGINE = INNODB;
CREATE TABLE nsm_target (target_id INT AUTO_INCREMENT, target_name VARCHAR(45) NOT NULL, target_description VARCHAR(100), target_class VARCHAR(80), target_type VARCHAR(45) NOT NULL, PRIMARY KEY(target_id)) ENGINE = INNODB;
CREATE TABLE nsm_target_value (tv_pt_id INT, tv_key VARCHAR(45), tv_val VARCHAR(45) NOT NULL, PRIMARY KEY(tv_pt_id, tv_key)) ENGINE = INNODB;
CREATE TABLE nsm_user (user_id INT AUTO_INCREMENT, user_account INT DEFAULT 0 NOT NULL, user_name VARCHAR(127) NOT NULL, user_lastname VARCHAR(40) NOT NULL, user_firstname VARCHAR(40) NOT NULL, user_password VARCHAR(64) NOT NULL, user_salt VARCHAR(64) NOT NULL, user_authsrc VARCHAR(45) DEFAULT 'internal' NOT NULL, user_authid VARCHAR(127), user_authkey VARCHAR(64), user_email VARCHAR(40) NOT NULL, user_disabled TINYINT DEFAULT '1' NOT NULL, user_created DATETIME NOT NULL, user_modified DATETIME NOT NULL, INDEX user_search_idx_idx (user_authsrc, user_authid, user_disabled), UNIQUE INDEX user_unique_idx (user_name), INDEX user_search_idx (user_name, user_authsrc, user_authid, user_disabled), PRIMARY KEY(user_id)) ENGINE = INNODB;
CREATE TABLE nsm_user_preference (upref_id INT AUTO_INCREMENT, upref_user_id INT NOT NULL, upref_val VARCHAR(100), upref_longval LONGTEXT, upref_key VARCHAR(50) NOT NULL, upref_created DATETIME NOT NULL, upref_modified DATETIME NOT NULL, INDEX upref_search_key_idx_idx (upref_key), INDEX principal_role_id_ix_idx (upref_user_id), PRIMARY KEY(upref_id)) ENGINE = INNODB;
CREATE TABLE nsm_user_role (usro_user_id INT, usro_role_id INT, INDEX nsm_user_role_ix_idx (usro_role_id), PRIMARY KEY(usro_user_id, usro_role_id)) ENGINE = INNODB;
ALTER TABLE cronk ADD CONSTRAINT cronk_cronk_user_id_nsm_user_user_id FOREIGN KEY (cronk_user_id) REFERENCES nsm_user(user_id);
ALTER TABLE cronk_category_cronk ADD CONSTRAINT cronk_category_cronk_ccc_cronk_id_cronk_cronk_id FOREIGN KEY (ccc_cronk_id) REFERENCES cronk(cronk_id);
ALTER TABLE cronk_category_cronk ADD CONSTRAINT cronk_category_cronk_ccc_cc_id_cronk_category_cc_id FOREIGN KEY (ccc_cc_id) REFERENCES cronk_category(cc_id);
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id FOREIGN KEY (cpc_cronk_id) REFERENCES cronk(cronk_id);
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT ccnp FOREIGN KEY (cpc_principal_id) REFERENCES nsm_principal(principal_id);
ALTER TABLE nsm_principal ADD CONSTRAINT nsm_principal_principal_user_id_nsm_user_user_id FOREIGN KEY (principal_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_principal ADD CONSTRAINT nsm_principal_principal_role_id_nsm_role_role_id FOREIGN KEY (principal_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_principal_target ADD CONSTRAINT nsm_principal_target_pt_target_id_nsm_target_target_id FOREIGN KEY (pt_target_id) REFERENCES nsm_target(target_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_principal_target ADD CONSTRAINT nsm_principal_target_pt_principal_id_nsm_principal_principal_id FOREIGN KEY (pt_principal_id) REFERENCES nsm_principal(principal_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_role ADD CONSTRAINT nsm_role_role_parent_nsm_role_role_id FOREIGN KEY (role_parent) REFERENCES nsm_role(role_id);
ALTER TABLE nsm_target_value ADD CONSTRAINT nsm_target_value_tv_pt_id_nsm_principal_target_pt_id FOREIGN KEY (tv_pt_id) REFERENCES nsm_principal_target(pt_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_user_preference ADD CONSTRAINT nsm_user_preference_upref_user_id_nsm_user_user_id FOREIGN KEY (upref_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_user_role ADD CONSTRAINT nsm_user_role_usro_user_id_nsm_user_user_id FOREIGN KEY (usro_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE nsm_user_role ADD CONSTRAINT nsm_user_role_usro_role_id_nsm_role_role_id FOREIGN KEY (usro_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE;


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
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('16','icinga.cronk.log','Enables icinga-log cronk','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('17','icinga.control.view','Allow user to view icinga status','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('18','icinga.control.admin','Allow user to administrate the icinga process','','credential');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled) VALUES ('1','icinga_user','The default representation of a ICINGA user','0');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled) VALUES ('2','appkit_user','Appkit user test','0');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_parent) VALUES ('3','appkit_admin','AppKit admin','0','2');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled) VALUES ('4','guest','Unauthorized Guest','0');
INSERT INTO nsm_user (user_id,user_account,user_name,user_firstname,user_lastname,user_password,user_salt,user_authsrc,user_email,user_disabled) VALUES ('1','0','root','Enoch','Root','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','internal','root@localhost.local','0');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','1');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','2');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','3');
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
