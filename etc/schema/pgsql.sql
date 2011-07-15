/*****************************************************/
/* Auto generated pgsql SQL Schema file for icinga-web*/
/* Creation date: 2011-02-11T13:26:19+01:00          */
/****************************************************/


/*           SQL schema defintiion        */
CREATE TABLE cronk (cronk_id SERIAL, cronk_uid VARCHAR(45), cronk_name VARCHAR(45), cronk_description VARCHAR(100), cronk_xml TEXT, cronk_user_id INT, cronk_created TIMESTAMP NOT NULL, cronk_modified TIMESTAMP NOT NULL, PRIMARY KEY(cronk_id));
CREATE TABLE cronk_category (cc_id SERIAL, cc_uid VARCHAR(45) NOT NULL, cc_name VARCHAR(45), cc_visible SMALLINT DEFAULT 0, cc_position INT DEFAULT 0, cc_created TIMESTAMP NOT NULL, cc_modified TIMESTAMP NOT NULL, PRIMARY KEY(cc_id));
CREATE TABLE cronk_category_cronk (ccc_cc_id INT, ccc_cronk_id INT, PRIMARY KEY(ccc_cc_id, ccc_cronk_id));
CREATE TABLE cronk_principal_cronk (cpc_principal_id INT, cpc_cronk_id INT, PRIMARY KEY(cpc_principal_id, cpc_cronk_id));
CREATE TABLE nsm_db_version (vers_id INT, version INT, PRIMARY KEY(vers_id));
CREATE TABLE nsm_log (log_id SERIAL, log_level INT NOT NULL, log_message TEXT NOT NULL, log_created TIMESTAMP NOT NULL, log_modified TIMESTAMP NOT NULL, PRIMARY KEY(log_id));
CREATE TABLE nsm_principal (principal_id SERIAL, principal_user_id INT, principal_role_id INT, principal_type VARCHAR(4) NOT NULL, principal_disabled SMALLINT DEFAULT 0, PRIMARY KEY(principal_id));
CREATE TABLE nsm_principal_target (pt_id SERIAL, pt_principal_id INT NOT NULL, pt_target_id INT NOT NULL, PRIMARY KEY(pt_id));
CREATE TABLE nsm_role (role_id SERIAL, role_name VARCHAR(40) NOT NULL, role_description VARCHAR(255), role_disabled SMALLINT, role_created TIMESTAMP NOT NULL, role_modified TIMESTAMP NOT NULL, role_parent INT, PRIMARY KEY(role_id));
CREATE TABLE nsm_session (session_entry_id SERIAL, session_id VARCHAR(255) NOT NULL, session_name VARCHAR(255) NOT NULL, session_data TEXT NOT NULL, session_checksum VARCHAR(255) NOT NULL, session_created TIMESTAMP NOT NULL, session_modified TIMESTAMP NOT NULL, PRIMARY KEY(session_entry_id));
CREATE TABLE nsm_target (target_id SERIAL, target_name VARCHAR(45) NOT NULL, target_description VARCHAR(100), target_class VARCHAR(80), target_type VARCHAR(45) NOT NULL, PRIMARY KEY(target_id));
CREATE TABLE nsm_target_value (tv_pt_id INT, tv_key VARCHAR(45), tv_val VARCHAR(45) NOT NULL, PRIMARY KEY(tv_pt_id, tv_key));
CREATE TABLE nsm_user (user_id SERIAL, user_account INT DEFAULT 0 NOT NULL, user_name VARCHAR(127) NOT NULL, user_lastname VARCHAR(40) NOT NULL, user_firstname VARCHAR(40) NOT NULL, user_password VARCHAR(64) NOT NULL, user_salt VARCHAR(64) NOT NULL, user_authsrc VARCHAR(45) DEFAULT 'internal' NOT NULL, user_authid VARCHAR(127), user_authkey VARCHAR(64), user_email VARCHAR(40) NOT NULL, user_disabled SMALLINT DEFAULT 1 NOT NULL, user_created TIMESTAMP NOT NULL, user_modified TIMESTAMP NOT NULL, PRIMARY KEY(user_id));
CREATE TABLE nsm_user_preference (upref_id SERIAL, upref_user_id INT NOT NULL, upref_val VARCHAR(100), upref_longval TEXT, upref_key VARCHAR(50) NOT NULL, upref_created TIMESTAMP NOT NULL, upref_modified TIMESTAMP NOT NULL, PRIMARY KEY(upref_id));
CREATE TABLE nsm_user_role (usro_user_id INT, usro_role_id INT, PRIMARY KEY(usro_user_id, usro_role_id));
CREATE UNIQUE INDEX cronk_uid_UNIQUE ON cronk (cronk_uid);
CREATE UNIQUE INDEX cc_uid_UNIQUE ON cronk_category (cc_uid);
CREATE INDEX pt_target_id_ix ON nsm_principal_target (pt_target_id);
CREATE INDEX pt_principal_id_ix ON nsm_principal_target (pt_principal_id);
CREATE UNIQUE INDEX user_unique ON nsm_user (user_name);
CREATE INDEX user_search_idx ON nsm_user (user_authsrc, user_authid, user_disabled);
CREATE INDEX user_search ON nsm_user (user_name, user_authsrc, user_authid, user_disabled);
CREATE INDEX upref_search_key_idx ON nsm_user_preference (upref_key);
CREATE INDEX principal_role_id_ix ON nsm_user_preference (upref_user_id);
CREATE INDEX nsm_user_role_ix ON nsm_user_role (usro_role_id);
ALTER TABLE cronk ADD CONSTRAINT cronk_cronk_user_id_nsm_user_user_id FOREIGN KEY (cronk_user_id) REFERENCES nsm_user(user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE cronk_category_cronk ADD CONSTRAINT cronk_category_cronk_ccc_cronk_id_cronk_cronk_id FOREIGN KEY (ccc_cronk_id) REFERENCES cronk(cronk_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE cronk_category_cronk ADD CONSTRAINT cronk_category_cronk_ccc_cc_id_cronk_category_cc_id FOREIGN KEY (ccc_cc_id) REFERENCES cronk_category(cc_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id FOREIGN KEY (cpc_cronk_id) REFERENCES cronk(cronk_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT ccnp FOREIGN KEY (cpc_principal_id) REFERENCES nsm_principal(principal_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal ADD CONSTRAINT nsm_principal_principal_user_id_nsm_user_user_id FOREIGN KEY (principal_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal ADD CONSTRAINT nsm_principal_principal_role_id_nsm_role_role_id FOREIGN KEY (principal_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal_target ADD CONSTRAINT nsm_principal_target_pt_target_id_nsm_target_target_id FOREIGN KEY (pt_target_id) REFERENCES nsm_target(target_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal_target ADD CONSTRAINT nsm_principal_target_pt_principal_id_nsm_principal_principal_id FOREIGN KEY (pt_principal_id) REFERENCES nsm_principal(principal_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_role ADD CONSTRAINT nsm_role_role_parent_nsm_role_role_id FOREIGN KEY (role_parent) REFERENCES nsm_role(role_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_target_value ADD CONSTRAINT nsm_target_value_tv_pt_id_nsm_principal_target_pt_id FOREIGN KEY (tv_pt_id) REFERENCES nsm_principal_target(pt_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_user_preference ADD CONSTRAINT nsm_user_preference_upref_user_id_nsm_user_user_id FOREIGN KEY (upref_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_user_role ADD CONSTRAINT nsm_user_role_usro_user_id_nsm_user_user_id FOREIGN KEY (usro_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_user_role ADD CONSTRAINT nsm_user_role_usro_role_id_nsm_role_role_id FOREIGN KEY (usro_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


/*          Initial data import              */
 
INSERT INTO nsm_user (user_id,user_account,user_name,user_firstname,user_lastname,user_password,user_salt,user_authsrc,user_email,user_disabled,user_created,user_modified) VALUES ('1','0','root','Enoch','Root','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','internal','root@localhost.local','0',now(),now());
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
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_modified,role_created) VALUES ('1','icinga_user','The default representation of a ICINGA user','0',now(),now());
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_modified,role_created) VALUES ('2','appkit_user','Appkit user test','0',now(),now());
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_parent,role_modified,role_created) VALUES ('3','appkit_admin','AppKit admin','0','2',now(),now());
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_modified,role_created) VALUES ('4','guest','Unauthorized Guest','0',now(),now());
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
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','1');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','2');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','3');

/*
Update sequences
*/
ALTER SEQUENCE nsm_user_user_id_seq RESTART WITH 2;
ALTER SEQUENCE nsm_target_target_id_seq RESTART WITH 19;
ALTER SEQUENCE nsm_role_role_id_seq RESTART WITH 5;
ALTER SEQUENCE nsm_principal_principal_id_seq RESTART WITH 6;
ALTER SEQUENCE nsm_principal_target_pt_id_seq RESTART WITH 10;

