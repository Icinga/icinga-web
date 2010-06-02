/*****************************************************/
/* Auto generated pgsql SQL Schema file for icinga-web*/
/* Creation date: 2010-06-02T10:07:34+02:00          */
/****************************************************/


/*           SQL schema defintiion        */
CREATE TABLE hm_colors (color_id SERIAL, preset_id INT NOT NULL, rgb VARCHAR(32), measure_value INT NOT NULL, PRIMARY KEY(color_id));
CREATE TABLE hm_dataSchemes (schema_id SERIAL, schema_regexp VARCHAR(64) DEFAULT '.*' NOT NULL, schema_target VARCHAR(25) DEFAULT 'perf' NOT NULL, schema_name VARCHAR(64), PRIMARY KEY(schema_id));
CREATE TABLE hm_heatmapPresets (hm_preset_id SERIAL, hm_preset_name VARCHAR(255) NOT NULL, hm_preset_falloff INT DEFAULT 30 NOT NULL, hm_preset_basetemperature INT DEFAULT 24 NOT NULL, PRIMARY KEY(hm_preset_id));
CREATE TABLE hm_roomEntities (entity_id SERIAL, room_id INT NOT NULL, data TEXT, PRIMARY KEY(entity_id));
CREATE TABLE hm_rooms (room_id SERIAL, room_name VARCHAR(64) NOT NULL, room_description TEXT, room_location VARCHAR(64), sizex INT, sizey INT, PRIMARY KEY(room_id));
CREATE TABLE hm_schedulerEntries (entry_id SERIAL, interval_s INT, name VARCHAR(255) NOT NULL, entry_heatmap_id INT NOT NULL, entry_room_id VARCHAR(16) NOT NULL, lastrun INT, suspended SMALLINT DEFAULT 0, days SMALLINT DEFAULT 127 NOT NULL, starttime VARCHAR(5) DEFAULT '00:00' NOT NULL, endtime VARCHAR(5) DEFAULT '24:00' NOT NULL, result VARCHAR(255) NOT NULL, path VARCHAR(1024) DEFAULT '/usr/local/icinga-web/app/modules/Heatmap/renderer/' NOT NULL, class VARCHAR(255) DEFAULT 'RenderJob' NOT NULL, args TEXT, forkable SMALLINT DEFAULT 1, fieldsize INT, PRIMARY KEY(entry_id));
CREATE TABLE hm_statestore (state_id SERIAL, state_name VARCHAR(64) NOT NULL, state_data TEXT NOT NULL, PRIMARY KEY(state_id));
CREATE TABLE msg_messages (message_id SERIAL, user_from INT, user_to INT, message_subject VARCHAR(255), message_content VARCHAR(255), PRIMARY KEY(message_id));
CREATE TABLE nsm_db_version (vers_id INT, version INT, PRIMARY KEY(vers_id));
CREATE TABLE nsm_log (log_id SERIAL, log_level INT NOT NULL, log_message TEXT NOT NULL, log_created TIMESTAMP NOT NULL, log_modified TIMESTAMP NOT NULL, PRIMARY KEY(log_id));
CREATE TABLE nsm_principal (principal_id SERIAL, principal_user_id INT, principal_role_id INT, principal_type VARCHAR(4) NOT NULL, principal_disabled SMALLINT DEFAULT 0, PRIMARY KEY(principal_id));
CREATE TABLE nsm_principal_target (pt_id SERIAL, pt_principal_id INT NOT NULL, pt_target_id INT NOT NULL, PRIMARY KEY(pt_id));
CREATE TABLE nsm_role (role_id SERIAL, role_name VARCHAR(40) NOT NULL, role_description VARCHAR(255), role_disabled SMALLINT DEFAULT 0 NOT NULL, role_created TIMESTAMP NOT NULL, role_modified TIMESTAMP NOT NULL, role_parent INT, PRIMARY KEY(role_id));
CREATE TABLE nsm_session (session_entry_id SERIAL, session_id VARCHAR(255) NOT NULL, session_name VARCHAR(255) NOT NULL, session_data BYTEA NOT NULL, session_checksum VARCHAR(255) NOT NULL, session_created TIMESTAMP NOT NULL, session_modified TIMESTAMP NOT NULL, PRIMARY KEY(session_entry_id));
CREATE TABLE nsm_target (target_id SERIAL, target_name VARCHAR(45) NOT NULL, target_description VARCHAR(100), target_class VARCHAR(80), target_type VARCHAR(45) NOT NULL, PRIMARY KEY(target_id));
CREATE TABLE nsm_target_value (tv_pt_id INT, tv_key VARCHAR(45), tv_val VARCHAR(45) NOT NULL, PRIMARY KEY(tv_pt_id, tv_key));
CREATE TABLE nsm_user (user_id SERIAL, user_account INT DEFAULT 0 NOT NULL, user_name VARCHAR(18) NOT NULL, user_lastname VARCHAR(40) NOT NULL, user_firstname VARCHAR(40) NOT NULL, user_password VARCHAR(64) NOT NULL, user_salt VARCHAR(64) NOT NULL, user_authsrc VARCHAR(45) DEFAULT 'internal' NOT NULL, user_authid VARCHAR(127), user_authkey VARCHAR(64), user_email VARCHAR(40) NOT NULL, user_disabled SMALLINT DEFAULT 1 NOT NULL, user_created TIMESTAMP NOT NULL, user_modified TIMESTAMP NOT NULL, PRIMARY KEY(user_id));
CREATE TABLE nsm_user_preference (upref_id SERIAL, upref_user_id INT NOT NULL, upref_val VARCHAR(100), upref_longval BYTEA, upref_key VARCHAR(50) NOT NULL, upref_created TIMESTAMP NOT NULL, upref_modified TIMESTAMP NOT NULL, PRIMARY KEY(upref_id));
CREATE TABLE nsm_user_role (usro_user_id INT, usro_role_id INT, PRIMARY KEY(usro_user_id, usro_role_id));
CREATE INDEX pt_target_id_ix ON nsm_principal_target (pt_target_id);
CREATE INDEX pt_principal_id_ix ON nsm_principal_target (pt_principal_id);
CREATE UNIQUE INDEX user_unique ON nsm_user (user_name);
CREATE INDEX user_unique_idx ON nsm_user (user_name);
CREATE INDEX user_search_idx ON nsm_user (user_name, user_authsrc, user_authid, user_disabled);
CREATE INDEX user_search ON nsm_user (user_name, user_authsrc, user_authid, user_disabled);
CREATE INDEX upref_search_key_idx ON nsm_user_preference (upref_key);
CREATE INDEX upref_search_key ON nsm_user_preference (upref_key);
CREATE INDEX principal_role_id_ix ON nsm_user_preference (upref_user_id);
CREATE INDEX nsm_user_role_ix ON nsm_user_role (usro_role_id);
ALTER TABLE msg_messages ADD CONSTRAINT msg_messages_user_to_nsm_user_user_id FOREIGN KEY (user_to) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE msg_messages ADD CONSTRAINT msg_messages_user_from_nsm_user_user_id FOREIGN KEY (user_from) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal ADD CONSTRAINT nsm_principal_principal_user_id_nsm_user_user_id FOREIGN KEY (principal_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal ADD CONSTRAINT nsm_principal_principal_role_id_nsm_role_role_id FOREIGN KEY (principal_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal_target ADD CONSTRAINT nsm_principal_target_pt_target_id_nsm_target_target_id FOREIGN KEY (pt_target_id) REFERENCES nsm_target(target_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_principal_target ADD CONSTRAINT nsm_principal_target_pt_principal_id_nsm_principal_principal_id FOREIGN KEY (pt_principal_id) REFERENCES nsm_principal(principal_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_role ADD CONSTRAINT nsm_role_role_parent_nsm_role_role_id FOREIGN KEY (role_parent) REFERENCES nsm_role(role_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_target_value ADD CONSTRAINT nsm_target_value_tv_pt_id_nsm_principal_target_pt_id FOREIGN KEY (tv_pt_id) REFERENCES nsm_principal_target(pt_id) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_user_preference ADD CONSTRAINT nsm_user_preference_upref_user_id_nsm_user_user_id FOREIGN KEY (upref_user_id) REFERENCES nsm_user(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_user_role ADD CONSTRAINT nsm_user_role_usro_user_id_nsm_user_user_id FOREIGN KEY (usro_user_id) REFERENCES nsm_user(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE nsm_user_role ADD CONSTRAINT nsm_user_role_usro_role_id_nsm_role_role_id FOREIGN KEY (usro_role_id) REFERENCES nsm_role(role_id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE;


/*          Initial data import              */
 
INSERT INTO nsm_user (user_id,user_account,user_name,user_firstname,user_lastname,user_password,user_salt,user_authsrc,user_authid,user_email,user_disabled) VALUES ('1','0','root','Enoch','Root','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','internal','','root@localhost.local','0');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled) VALUES ('1','icinga_user','The default representation of a ICINGA user','0');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled) VALUES ('2','appkit_user','Appkit user test','0');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_parent) VALUES ('3','appkit_admin','AppKit admin','0','2');
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled) VALUES ('4','guest','Unauthorized Guest','0');
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
INSERT INTO nsm_principal (principal_id,principal_user_id,principal_type,principal_disabled) VALUES ('1','1','user','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('2','2','role','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('3','3','role','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('4','1','role','0');
INSERT INTO nsm_principal (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('5','4','role','0');
INSERT INTO nsm_db_version (vers_id,version) VALUES ('1','2');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('1','2','8');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('2','3','9');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('3','3','10');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('4','3','11');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('5','4','8');
INSERT INTO nsm_principal_target (pt_id,pt_principal_id,pt_target_id) VALUES ('6','5','7');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','1');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','2');
INSERT INTO nsm_user_role (usro_user_id,usro_role_id) VALUES ('1','3');