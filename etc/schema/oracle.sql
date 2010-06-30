/*****************************************************/
/* Auto generated oracle SQL Schema file for icinga-web*/
/* Creation date: 2010-06-30T09:11:48+02:00          */
/****************************************************/


/*           SQL schema defintiion        */
CREATE TABLE nsm_db_version (vers_id NUMBER(10), version NUMBER(10), PRIMARY KEY(vers_id))
/
CREATE TABLE nsm_log (log_id NUMBER(10), log_level NUMBER(10) NOT NULL, log_message CLOB NOT NULL, log_created DATE NOT NULL, log_modified DATE NOT NULL, PRIMARY KEY(log_id))
/
CREATE TABLE nsm_principal (principal_id NUMBER(10), principal_user_id NUMBER(10), principal_role_id NUMBER(10), principal_type VARCHAR2(4) NOT NULL, principal_disabled NUMBER(3) DEFAULT 0, PRIMARY KEY(principal_id))
/
CREATE TABLE nsm_principal_target (pt_id NUMBER(10), pt_principal_id NUMBER(10) NOT NULL, pt_target_id NUMBER(10) NOT NULL, PRIMARY KEY(pt_id))
/
CREATE TABLE nsm_role (role_id NUMBER(10), role_name VARCHAR2(40) NOT NULL, role_description VARCHAR2(255), role_disabled NUMBER(3) DEFAULT 0 NOT NULL, role_created DATE NOT NULL, role_modified DATE NOT NULL, role_parent NUMBER(10), PRIMARY KEY(role_id))
/
CREATE TABLE nsm_session (session_entry_id NUMBER(10), session_id VARCHAR2(255) NOT NULL, session_name VARCHAR2(255) NOT NULL, session_data CLOB NOT NULL, session_checksum VARCHAR2(255) NOT NULL, session_created DATE NOT NULL, session_modified DATE NOT NULL, PRIMARY KEY(session_entry_id))
/
CREATE TABLE nsm_target (target_id NUMBER(10), target_name VARCHAR2(45) NOT NULL, target_description VARCHAR2(100), target_class VARCHAR2(80), target_type VARCHAR2(45) NOT NULL, PRIMARY KEY(target_id))
/
CREATE TABLE nsm_target_value (tv_pt_id NUMBER(10), tv_key VARCHAR2(45), tv_val VARCHAR2(45) NOT NULL, PRIMARY KEY(tv_pt_id, tv_key))
/
CREATE TABLE nsm_user (user_id NUMBER(10), user_account NUMBER(10) DEFAULT 0 NOT NULL, user_name VARCHAR2(127) NOT NULL, user_lastname VARCHAR2(40) NOT NULL, user_firstname VARCHAR2(40) NOT NULL, user_password VARCHAR2(64) NOT NULL, user_salt VARCHAR2(64) NOT NULL, user_authsrc VARCHAR2(45) DEFAULT 'internal' NOT NULL, user_authid VARCHAR2(127), user_authkey VARCHAR2(64), user_email VARCHAR2(40) NOT NULL, user_disabled NUMBER(3) DEFAULT 1 NOT NULL, user_created DATE NOT NULL, user_modified DATE NOT NULL, PRIMARY KEY(user_id), CONSTRAINT user_unique UNIQUE (user_name))
/
CREATE TABLE nsm_user_preference (upref_id NUMBER(10), upref_user_id NUMBER(10) NOT NULL, upref_val VARCHAR2(100), upref_longval CLOB, upref_key VARCHAR2(50) NOT NULL, upref_created DATE NOT NULL, upref_modified DATE NOT NULL, PRIMARY KEY(upref_id))
/
CREATE TABLE nsm_user_role (usro_user_id NUMBER(10), usro_role_id NUMBER(10), PRIMARY KEY(usro_user_id, usro_role_id))
/
CREATE SEQUENCE NSM_LOG_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_PRINCIPAL_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_PRINCIPAL_TARGET_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_ROLE_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_SESSION_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_TARGET_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_USER_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE SEQUENCE NSM_USER_PREFERENCE_seq START WITH 1 INCREMENT BY 1 NOCACHE
/
CREATE INDEX pt_target_id_ix ON nsm_principal_target (pt_target_id)
/
CREATE INDEX pt_principal_id_ix ON nsm_principal_target (pt_principal_id)
/
CREATE INDEX user_search_idx ON nsm_user (user_authsrc, user_authid, user_disabled)
/
CREATE INDEX user_search ON nsm_user (user_name, user_authsrc, user_authid, user_disabled)
/
CREATE INDEX upref_search_key_idx ON nsm_user_preference (upref_key)
/
CREATE INDEX principal_role_id_ix ON nsm_user_preference (upref_user_id)
/
CREATE INDEX nsm_user_role_ix ON nsm_user_role (usro_role_id)
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_LOG' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_LOG ADD CONSTRAINT NSM_LOG_AI_PK_idx PRIMARY KEY (log_id)';
  END IF;
END;
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_PRINCIPAL' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_PRINCIPAL ADD CONSTRAINT NSM_PRINCIPAL_AI_PK_idx PRIMARY KEY (principal_id)';
  END IF;
END;
/
ALTER TABLE nsm_principal ADD CONSTRAINT npnu FOREIGN KEY (principal_user_id) REFERENCES nsm_user(user_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE nsm_principal ADD CONSTRAINT npnr FOREIGN KEY (principal_role_id) REFERENCES nsm_role(role_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_PRINCIPAL_TARGET' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_PRINCIPAL_TARGET ADD CONSTRAINT NSM_PRINCIPAL_TARGET_AI_PK_idx PRIMARY KEY (pt_id)';
  END IF;
END;
/
ALTER TABLE nsm_principal_target ADD CONSTRAINT npnt FOREIGN KEY (pt_target_id) REFERENCES nsm_target(target_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE nsm_principal_target ADD CONSTRAINT npnp_1 FOREIGN KEY (pt_principal_id) REFERENCES nsm_principal(principal_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_ROLE' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_ROLE ADD CONSTRAINT NSM_ROLE_AI_PK_idx PRIMARY KEY (role_id)';
  END IF;
END;
/
ALTER TABLE nsm_role ADD CONSTRAINT nrnr FOREIGN KEY (role_parent) REFERENCES nsm_role(role_id) NOT DEFERRABLE INITIALLY IMMEDIATE
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_SESSION' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_SESSION ADD CONSTRAINT NSM_SESSION_AI_PK_idx PRIMARY KEY (session_entry_id)';
  END IF;
END;
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_TARGET' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_TARGET ADD CONSTRAINT NSM_TARGET_AI_PK_idx PRIMARY KEY (target_id)';
  END IF;
END;
/
ALTER TABLE nsm_target_value ADD CONSTRAINT ntnp_1 FOREIGN KEY (tv_pt_id) REFERENCES nsm_principal_target(pt_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_USER' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_USER ADD CONSTRAINT NSM_USER_AI_PK_idx PRIMARY KEY (user_id)';
  END IF;
END;
/
DECLARE
  constraints_Count NUMBER;
BEGIN
  SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count FROM USER_CONSTRAINTS WHERE TABLE_NAME = 'NSM_USER_PREFERENCE' AND CONSTRAINT_TYPE = 'P';
  IF constraints_Count = 0 THEN
    EXECUTE IMMEDIATE 'ALTER TABLE NSM_USER_PREFERENCE ADD CONSTRAINT NSM_USER_PREFERENCE_AI_PK_idx PRIMARY KEY (upref_id)';
  END IF;
END;
/
ALTER TABLE nsm_user_preference ADD CONSTRAINT nunu_4 FOREIGN KEY (upref_user_id) REFERENCES nsm_user(user_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE nsm_user_role ADD CONSTRAINT nunu_5 FOREIGN KEY (usro_user_id) REFERENCES nsm_user(user_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
ALTER TABLE nsm_user_role ADD CONSTRAINT nunr FOREIGN KEY (usro_role_id) REFERENCES nsm_role(role_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
/
CREATE TRIGGER NSM_LOG_AI_PK
   BEFORE INSERT
   ON NSM_LOG
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.log_id IS NULL OR :NEW.log_id = 0) THEN
      SELECT NSM_LOG_seq.NEXTVAL INTO :NEW.log_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_LOG_seq');
      SELECT :NEW.log_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_LOG_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_PRINCIPAL_AI_PK
   BEFORE INSERT
   ON NSM_PRINCIPAL
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.principal_id IS NULL OR :NEW.principal_id = 0) THEN
      SELECT NSM_PRINCIPAL_seq.NEXTVAL INTO :NEW.principal_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_PRINCIPAL_seq');
      SELECT :NEW.principal_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_PRINCIPAL_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_PRINCIPAL_TARGET_AI_PK
   BEFORE INSERT
   ON NSM_PRINCIPAL_TARGET
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.pt_id IS NULL OR :NEW.pt_id = 0) THEN
      SELECT NSM_PRINCIPAL_TARGET_seq.NEXTVAL INTO :NEW.pt_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_PRINCIPAL_TARGET_seq');
      SELECT :NEW.pt_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_PRINCIPAL_TARGET_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_ROLE_AI_PK
   BEFORE INSERT
   ON NSM_ROLE
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.role_id IS NULL OR :NEW.role_id = 0) THEN
      SELECT NSM_ROLE_seq.NEXTVAL INTO :NEW.role_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_ROLE_seq');
      SELECT :NEW.role_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_ROLE_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_SESSION_AI_PK
   BEFORE INSERT
   ON NSM_SESSION
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.session_entry_id IS NULL OR :NEW.session_entry_id = 0) THEN
      SELECT NSM_SESSION_seq.NEXTVAL INTO :NEW.session_entry_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_SESSION_seq');
      SELECT :NEW.session_entry_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_SESSION_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_TARGET_AI_PK
   BEFORE INSERT
   ON NSM_TARGET
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.target_id IS NULL OR :NEW.target_id = 0) THEN
      SELECT NSM_TARGET_seq.NEXTVAL INTO :NEW.target_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_TARGET_seq');
      SELECT :NEW.target_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_TARGET_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_USER_AI_PK
   BEFORE INSERT
   ON NSM_USER
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.user_id IS NULL OR :NEW.user_id = 0) THEN
      SELECT NSM_USER_seq.NEXTVAL INTO :NEW.user_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_USER_seq');
      SELECT :NEW.user_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_USER_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/
CREATE TRIGGER NSM_USER_PREFERENCE_AI_PK
   BEFORE INSERT
   ON NSM_USER_PREFERENCE
   FOR EACH ROW
DECLARE
   last_Sequence NUMBER;
   last_InsertID NUMBER;
BEGIN
   IF (:NEW.upref_id IS NULL OR :NEW.upref_id = 0) THEN
      SELECT NSM_USER_PREFERENCE_seq.NEXTVAL INTO :NEW.upref_id FROM DUAL;
   ELSE
      SELECT NVL(Last_Number, 0) INTO last_Sequence
        FROM User_Sequences
       WHERE UPPER(Sequence_Name) = UPPER('NSM_USER_PREFERENCE_seq');
      SELECT :NEW.upref_id INTO last_InsertID FROM DUAL;
      WHILE (last_InsertID > last_Sequence) LOOP
         SELECT NSM_USER_PREFERENCE_seq.NEXTVAL INTO last_Sequence FROM DUAL;
      END LOOP;
   END IF;
END;
/


/*          Initial data import              */
 
INSERT INTO NSM_USER (user_id,user_account,user_name,user_firstname,user_lastname,user_password,user_salt,user_authsrc,user_email,user_disabled) VALUES ('1','0','root','Enoch','Root','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','internal','root@localhost.local','0');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('1','IcingaHostgroup','Limit data access to specific hostgroups','IcingaDataHostgroupPrincipalTarget','icinga');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('2','IcingaServicegroup','Limit data access to specific servicegroups','IcingaDataServicegroupPrincipalTarget','icinga');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('3','IcingaHostCustomVariablePair','Limit data access to specific custom variables','IcingaDataHostCustomVariablePrincipalTarget','icinga');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('4','IcingaServiceCustomVariablePair','Limit data access to specific custom variables','IcingaDataServiceCustomVariablePrincipalTarget','icinga');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('5','IcingaContactgroup','Limit data access to users contact group membership','IcingaDataContactgroupPrincipalTarget','icinga');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('6','IcingaCommandRo','Limit access to commands','IcingaDataCommandRoPrincipalTarget','icinga');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('7','appkit.access','Access to login-page (which, actually, means no access)','','credential');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('8','icinga.user','Access to icinga','','credential');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('9','appkit.admin.groups','Access to group editor','','credential');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('10','appkit.admin.users','Access to user editor','','credential');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('11','appkit.admin','Access to admin panel ','','credential');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('12','appkit.user.dummy','Basic right for users','','credential');
INSERT INTO NSM_TARGET (target_id,target_name,target_description,target_class,target_type) VALUES ('13','appkit.api.access','Access to web-based api adapter','','credential');
INSERT INTO NSM_ROLE (role_id,role_name,role_description,role_disabled) VALUES ('1','icinga_user','The default representation of a ICINGA user','0');
INSERT INTO NSM_ROLE (role_id,role_name,role_description,role_disabled) VALUES ('2','appkit_user','Appkit user test','0');
INSERT INTO NSM_ROLE (role_id,role_name,role_description,role_disabled,role_parent) VALUES ('3','appkit_admin','AppKit admin','0','2');
INSERT INTO NSM_ROLE (role_id,role_name,role_description,role_disabled) VALUES ('4','guest','Unauthorized Guest','0');
INSERT INTO NSM_DB_VERSION (vers_id,version) VALUES ('1','2');
INSERT INTO NSM_USER_ROLE (usro_user_id,usro_role_id) VALUES ('1','1');
INSERT INTO NSM_USER_ROLE (usro_user_id,usro_role_id) VALUES ('1','2');
INSERT INTO NSM_USER_ROLE (usro_user_id,usro_role_id) VALUES ('1','3');
INSERT INTO NSM_PRINCIPAL (principal_id,principal_user_id,principal_type,principal_disabled) VALUES ('1','1','user','0');
INSERT INTO NSM_PRINCIPAL (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('2','2','role','0');
INSERT INTO NSM_PRINCIPAL (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('3','3','role','0');
INSERT INTO NSM_PRINCIPAL (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('4','1','role','0');
INSERT INTO NSM_PRINCIPAL (principal_id,principal_role_id,principal_type,principal_disabled) VALUES ('5','4','role','0');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('1','2','8');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('2','2','13');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('3','3','9');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('4','3','10');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('5','3','11');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('6','4','8');
INSERT INTO NSM_PRINCIPAL_TARGET (pt_id,pt_principal_id,pt_target_id) VALUES ('7','5','7');