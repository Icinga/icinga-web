/*
-- ****************************************************
-- based on oracle.sql supplied with icinga-web-1.7.0.tar.gz
-- Auto generated oracle SQL Schema file for icinga-web
-- Creation date: 2011-02-13T21:02:36+01:00          
-- ****************************************************
-- 
-- look for DEFINE commands and check for your needs !!!!
--
-- check standard database connectivity with test connect to <instance> with sqlplus
--
-- create icinga database user and tablespaces if needed , run sqlplus, replacing
-- >sqlplus "sys@<instance> as sysdba" @create_icingaweb_sys.sql
-- enter password
-- check for errors in create_icingaweb_sys.log
--
-- create icinga objects
-- sqlplus <icinga-web-user-just-created>@<instance> @create_icingaweb_objects
-- enter password
-- check for errors in create_icingaweb_objects.log
--
-- initial version: 2012-03-07 Thomas Dreßler
-- current version: 2013-03-17 Thomas Dreßler
-- -- --------------------------------------------------------
*/
set sqlprompt "&&_USER@&&_CONNECT_IDENTIFIER SQL>"

/* drop all objects */
prompt Press Enter to drop all existing objects in schema &&_USER or CTRL-C to interrupt! 
accept x
set pagesize 200;
set linesize 200;
set heading off;
set echo off;
set feedback off;

spool drop_objects.sql;
/* drop tables cascade, this will also drop dependend objects like index, contraints,lob */
select 'drop '||object_type||' '||object_name||' cascade constraints;' from user_objects where object_type='TABLE';
select 'drop '||object_type||' '||object_name||';' from user_objects where object_type not in ('TABLE','INDEX','TRIGGER','PACKAGE BODY','LOB');
prompt
select 'PURGE RECYCLEBIN;' from dual;
select 'select * from user_objects;' from dual;
prompt spool off;
spool off;
set heading on;
set echo on;
set feedback on;
spool drop_objects;
@drop_objects.sql;

-- set escape character
SET ESCAPE \

/* set real TBS names , no checks implemented!*/
define DATATBS='ICINGAWEB_DATA1';
define LOBTBS='ICINGAWEB_DATA1';
define IXTBS='ICINGAWEB_IDX1';


/* error handling */
whenever sqlerror exit failure

/* logging */
spool create_icingaweb_objects.log

/*Icunga tables */
CREATE TABLE cronk
  (
    cronk_id          NUMBER(10),
    cronk_uid         VARCHAR2(45),
    cronk_name        VARCHAR2(45),
    cronk_description VARCHAR2(100),
    cronk_xml CLOB,
    cronk_user_id NUMBER(10),
    cronk_created DATE default sysdate,
    cronk_modified DATE default sysdate,
    cronk_system NUMBER(3) DEFAULT 0
    )
    lob (cronk_xml) store as cronk_xml_lob(tablespace &LOBTBS)
    tablespace &DATATBS;
alter table cronk add constraint cronk_pk PRIMARY KEY  (cronk_id)
	using index tablespace &IXTBS;
alter table cronk add constraint  cronk_uq UNIQUE (cronk_uid)
	using index tablespace &IXTBS;

CREATE TABLE cronk_category
  (
    cc_id       NUMBER(10),
    cc_uid      VARCHAR2(45),
    cc_name     VARCHAR2(45),
    cc_visible  NUMBER(3) DEFAULT 0,
    cc_position NUMBER(10) DEFAULT 0,
    cc_created DATE default sysdate,
    cc_modified DATE default sysdate,
    cc_system NUMBER(3) DEFAULT 0
  )
  tablespace &DATATBS;
alter table cronk_category add constraint cronk_cat_pk PRIMARY KEY  (cc_id)
	using index tablespace &IXTBS;
alter table cronk_category add constraint  cronk_cat_uq UNIQUE (cc_uid)
	using index tablespace &IXTBS;

--use index organized table because all data is within index  
CREATE TABLE cronk_category_cronk
  (
    ccc_cc_id    NUMBER(10),
    ccc_cronk_id NUMBER(10),
    constraint CCC_PK PRIMARY KEY(ccc_cc_id, ccc_cronk_id)
  )
  organization index 
  tablespace &DATATBS;
  
--use index organized table because all data is within index
CREATE TABLE cronk_principal_cronk
  (
    cpc_principal_id NUMBER(10),
    cpc_cronk_id     NUMBER(10),
    constraint CPC_PK PRIMARY KEY(cpc_principal_id, cpc_cronk_id)
  )
  organization index 
  tablespace &DATATBS;

--use index organized table because all data is within index
CREATE TABLE cronk_principal_category
  (
    principal_id NUMBER(10),
    category_id     NUMBER(10),
    constraint CPCAT_PK PRIMARY KEY(principal_id, category_id)
  )
  organization index
  tablespace &IXTBS;

CREATE TABLE nsm_db_version
  (
    id NUMBER(10),
    version VARCHAR2(32),
	modified timestamp with local time zone,
	created timestamp with local time zone
  )
  tablespace &DATATBS;
alter table nsm_db_version add constraint nsm_db_version_pk PRIMARY KEY  (id)
	using index tablespace &IXTBS;

CREATE TABLE nsm_log
  (
    log_id    NUMBER(10),
    log_level NUMBER(10),
    log_message CLOB,
    log_created DATE default sysdate,
    log_modified DATE default sysdate
  )
  lob (log_message) store as log_msg_lob(tablespace &LOBTBS)
  tablespace &DATATBS;
alter table nsm_log add constraint nsm_log_pk PRIMARY KEY  (log_id)
	using index tablespace &IXTBS;
  
CREATE TABLE nsm_principal
  (
    principal_id       NUMBER(10),
    principal_user_id  NUMBER(10),
    principal_role_id  NUMBER(10),
    principal_type     VARCHAR2(4) default '',
    principal_disabled NUMBER(3) DEFAULT 0
  )
  tablespace &DATATBS;
alter table nsm_principal add constraint nsm_principal_pk PRIMARY KEY  (principal_id)
	using index tablespace &IXTBS;
  
CREATE TABLE nsm_principal_target
  (
    pt_id           NUMBER(10),
    pt_principal_id NUMBER(10) default 0,
    pt_target_id    NUMBER(10) default 0
  )
  tablespace &DATATBS;
alter table nsm_principal_target add constraint nsm_pr_target_pk PRIMARY KEY  (pt_id)
	using index tablespace &IXTBS;
CREATE TABLE nsm_role
  (
    role_id          NUMBER(10),
    role_name        VARCHAR2(40) default 'default',
    role_description VARCHAR2(255),
    role_disabled    NUMBER(3) DEFAULT 0,
    role_created DATE default sysdate,
    role_modified DATE default sysdate,
    role_parent NUMBER(10)
  )
  tablespace &DATATBS;

alter table nsm_role add constraint nsm_role_pk PRIMARY KEY  (role_id)
	using index tablespace &IXTBS;
  
CREATE TABLE nsm_session
  (
    session_entry_id NUMBER(10),
    session_id       VARCHAR2(255) default 'none',
    session_name     VARCHAR2(255) default 'default',
    session_data CLOB,
    session_checksum VARCHAR2(255),
    session_created DATE default sysdate,
    session_modified DATE default sysdate
  )
  lob (session_data) store as session_data_lob(tablespace &LOBTBS)
  tablespace &DATATBS;
alter table nsm_session add constraint nsm_session_pk PRIMARY KEY  (session_entry_id)
	using index tablespace &IXTBS;

CREATE TABLE nsm_target
  (
    target_id          NUMBER(10),
    target_name        VARCHAR2(45) default 'default',
    target_description VARCHAR2(100),
    target_class       VARCHAR2(80),
    target_type        VARCHAR2(45) default 'default'
  )
  tablespace &DATATBS;
alter table nsm_target add constraint nsm_target_pk PRIMARY KEY  (target_id)
  using index tablespace &IXTBS;

ALTER TABLE
  nsm_target
  add constraint target_key_unq_tgt_n_uq UNIQUE (target_name)
  using index tablespace &IXTBS;

--use index organized table because most of all data is within index  
CREATE TABLE nsm_target_value
  (
    tv_pt_id NUMBER(10),
    tv_key   VARCHAR2(45),
    tv_val   VARCHAR2(45) default 'default',
    constraint nsm_target_value_pk PRIMARY KEY(tv_pt_id, tv_key)
  )
  organization index
  tablespace &DATATBS;

CREATE TABLE nsm_user
  (
    user_id        NUMBER(10),
    user_account   NUMBER(10) DEFAULT 0,
    user_name      VARCHAR2(127) default 'default_name',
    user_lastname  VARCHAR2(40) ,
    user_firstname VARCHAR2(40) ,
    user_password  VARCHAR2(64) ,
    user_salt      VARCHAR2(64) ,
    user_authsrc   VARCHAR2(45) DEFAULT 'internal',
    user_authid    VARCHAR2(512),
    user_authkey   VARCHAR2(64),
    user_email     VARCHAR2(254) ,
    user_description VARCHAR2(255) ,
    user_disabled  NUMBER(3) DEFAULT 1 ,
    user_created DATE default sysdate,
    user_modified DATE default sysdate,
    user_last_login DATE
  )
  tablespace &DATATBS;
alter table nsm_user add constraint nsm_user_pk PRIMARY KEY  (user_id)
	using index tablespace &IXTBS;
alter table nsm_user add constraint nsm_user_uq UNIQUE  (user_name)
	using index tablespace &IXTBS;

  
CREATE TABLE nsm_user_preference
  (
    upref_id      NUMBER(10),
    upref_user_id NUMBER(10),
    upref_val     VARCHAR2(100),
    upref_longval CLOB,
    upref_key VARCHAR2(50) default'default',
    upref_created DATE default sysdate,
    upref_modified DATE default sysdate
  )
  lob (upref_longval) store as upref_longval_lob(tablespace &LOBTBS)
  tablespace &DATATBS;
alter table nsm_user_preference add constraint nsm_user_pref_pk PRIMARY KEY  (upref_id)
	using index tablespace &IXTBS;
alter table nsm_user_preference add constraint nsm_user_pref_userid_key_uq UNIQUE (upref_user_id, upref_key)
  using index tablespace &IXTBS; 
  
  --use index organized table because all data is within index
CREATE TABLE nsm_user_role
  (
    usro_user_id NUMBER(10),
    usro_role_id NUMBER(10),
    constraint nsm_user_role_pk PRIMARY KEY(usro_user_id, usro_role_id)
  )
  organization index
  tablespace &DATATBS;

  /* sequences */
CREATE SEQUENCE CRONK_seq  START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE CRONK_CATEGORY_seq START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE NSM_LOG_seq START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE NSM_PRINCIPAL_seq START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE NSM_PRINCIPAL_TARGET_seq START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE NSM_ROLE_seq START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE NSM_SESSION_seq START WITH 1 INCREMENT BY 1 NOCACHE; 
CREATE SEQUENCE NSM_TARGET_seq START WITH 1 INCREMENT BY 1 NOCACHE; 
CREATE SEQUENCE NSM_USER_seq START WITH 1 INCREMENT BY 1 NOCACHE; 
CREATE SEQUENCE NSM_USER_PREFERENCE_seq START WITH 1 INCREMENT BY 1 NOCACHE;

/* add index*/
CREATE INDEX pt_target_id_idx ON nsm_principal_target (pt_target_id)
  tablespace &IXTBS;
CREATE INDEX pt_principal_id_idx ON nsm_principal_target (pt_principal_id)
  tablespace &IXTBS;
CREATE INDEX nu_user_search_idx ON nsm_user (user_authsrc,user_authid,user_disabled)  
tablespace &IXTBS;  
CREATE INDEX nu_user_search_ix2 ON nsm_user (user_name, user_authsrc,user_authid, user_disabled)  
  tablespace &IXTBS;
CREATE INDEX nup_upref_key_idx ON nsm_user_preference (upref_key )
    tablespace &IXTBS;
CREATE INDEX nup_upref_user_idx ON nsm_user_preference (upref_user_id)
  tablespace &IXTBS;
CREATE INDEX nur_user_role_idx ON nsm_user_role ( usro_role_id )
  tablespace &IXTBS;


/* foreign keys, requires referenced tabls exists with pk */
ALTER TABLE cronk ADD CONSTRAINT ccnu_fk FOREIGN KEY (cronk_user_id) 
	REFERENCES nsm_user(user_id);

ALTER TABLE cronk_category_cronk ADD CONSTRAINT ccc_cid_fk FOREIGN KEY (ccc_cronk_id) 
  REFERENCES cronk(cronk_id);
ALTER TABLE cronk_category_cronk ADD CONSTRAINT ccc_ccid_fk FOREIGN KEY (ccc_cc_id) 
  REFERENCES cronk_category(cc_id);
ALTER TABLE cronk_principal_category ADD CONSTRAINT cpcat_pi_fk FOREIGN KEY (principal_id) 
  REFERENCES nsm_principal(principal_id);
ALTER TABLE cronk_principal_category ADD CONSTRAINT cpcat_ci_fk FOREIGN KEY (category_id) 
  REFERENCES cronk_category(cc_id);
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT cpc_pi_fk FOREIGN KEY (cpc_principal_id) 
  REFERENCES nsm_principal(principal_id);
ALTER TABLE cronk_principal_cronk ADD CONSTRAINT cpc__ci_fk FOREIGN KEY (cpc_cronk_id) 
  REFERENCES cronk(cronk_id);
ALTER TABLE nsm_principal ADD CONSTRAINT np_nu_fk FOREIGN KEY (principal_user_id) 
  REFERENCES nsm_user(user_id) ON DELETE CASCADE;
ALTER TABLE nsm_principal ADD CONSTRAINT np_nr_fk FOREIGN KEY (principal_role_id) 
  REFERENCES nsm_role(role_id) ON DELETE CASCADE; 
ALTER TABLE nsm_principal_target ADD CONSTRAINT npt_nt_fk FOREIGN KEY (pt_target_id) 
  REFERENCES nsm_target(target_id) ON DELETE CASCADE ; 
ALTER TABLE nsm_principal_target ADD CONSTRAINT npt_np_fk FOREIGN KEY (pt_principal_id) 
  REFERENCES nsm_principal(principal_id) ON DELETE CASCADE; 
ALTER TABLE nsm_role ADD CONSTRAINT nr_nr_fk FOREIGN KEY (role_parent) 
  REFERENCES nsm_role(role_id);
ALTER TABLE nsm_target_value ADD CONSTRAINT ntv_npt_fk FOREIGN KEY (tv_pt_id) 
  REFERENCES nsm_principal_target(pt_id) ON DELETE CASCADE; 
ALTER TABLE nsm_user_preference ADD CONSTRAINT nup_nu_fk FOREIGN KEY (upref_user_id) 
  REFERENCES nsm_user(user_id) ON DELETE CASCADE ;
ALTER TABLE nsm_user_role ADD CONSTRAINT nur_nu_fk FOREIGN KEY (usro_user_id) 
  REFERENCES nsm_user(user_id) ON DELETE CASCADE; 
ALTER TABLE nsm_user_role ADD CONSTRAINT nur_nr_fk FOREIGN KEY (usro_role_id) 
REFERENCES nsm_role(role_id) ON DELETE CASCADE ;



/* autoid trigger */
CREATE or REPLACE TRIGGER CRONK_AI_PK_TRG 
  BEFORE INSERT ON CRONK 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT CRONK_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.cronk_id IS NULL OR :New.cronk_id < next_val) THEN
    --assign sequence value to table
    :NEW.cronk_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.cronk_id > next_val)
    LOOP
      SELECT CRONK_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/

CREATE or REPLACE TRIGGER CRONK_CATEGORY_AI_TRG 
  BEFORE   INSERT ON CRONK_CATEGORY 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT CRONK_CATEGORY_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.cc_id IS NULL OR :New.cc_id < next_val) THEN
    --assign sequence value to table
    :NEW.cc_id:=next_val;
  ELSE
  --update sequence counter to fit last value
    WHILE (:NEW.cc_id > next_val)
    LOOP
      SELECT CRONK_CATEGORY_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/

CREATE or REPLACE TRIGGER NSM_LOG_AI_TRG 
  BEFORE INSERT ON NSM_LOG 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_LOG_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.log_id IS NULL OR :New.log_id < next_val) THEN
    --assign sequence value to table
    :NEW.log_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.log_id > next_val)
    LOOP
      SELECT NSM_LOG_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/
  
CREATE or REPLACE TRIGGER NSM_PRINCIPAL_AI_TRG
BEFORE INSERT ON NSM_PRINCIPAL 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_PRINCIPAL_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.principal_id IS NULL OR :New.principal_id < next_val) THEN
    --assign sequence value to table
    :NEW.principal_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.principal_id > next_val)
    LOOP
      SELECT NSM_PRINCIPAL_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/
  
CREATE or REPLACE TRIGGER NSM_PRINCIPAL_TARGET_AI_TRG
  BEFORE INSERT ON NSM_PRINCIPAL_TARGET 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_PRINCIPAL_TARGET_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.pt_id IS NULL OR :New.pt_id < next_val) THEN
    --assign sequence value to table
    :NEW.pt_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.pt_id > next_val)
    LOOP
      SELECT NSM_PRINCIPAL_TARGET_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/


CREATE or REPLACE TRIGGER NSM_ROLE_AI_TRG
  BEFORE INSERT ON NSM_ROLE 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_ROLE_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.role_id IS NULL OR :New.role_id < next_val) THEN
    --assign sequence value to table
    :NEW.role_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.role_id > next_val)
    LOOP
      SELECT NSM_ROLE_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/

CREATE or REPLACE TRIGGER NSM_SESSION_AI_TRG
  BEFORE INSERT ON NSM_SESSION 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_SESSION_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.session_entry_id IS NULL OR :New.session_entry_id < next_val) THEN
    --assign sequence value to table
    :NEW.session_entry_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.session_entry_id > next_val)
    LOOP
      SELECT NSM_SESSION_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/

CREATE or REPLACE TRIGGER NSM_TARGET_AI_TRG
  BEFORE INSERT ON NSM_TARGET 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_TARGET_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.target_id IS NULL OR :New.target_id < next_val) THEN
    --assign sequence value to table
    :NEW.target_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.target_id > next_val)
    LOOP
      SELECT NSM_TARGET_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/

CREATE or REPLACE TRIGGER NSM_USER_AI_TRG
  BEFORE INSERT ON NSM_USER 
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_USER_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.user_id IS NULL OR :New.user_id < next_val) THEN
    --assign sequence value to table
    :NEW.user_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.user_id > next_val)
    LOOP
      SELECT NSM_USER_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/

CREATE or REPLACE TRIGGER NSM_USER_PREFERENCE_AI_TRG
  BEFORE INSERT ON NSM_USER_PREFERENCE
  FOR EACH ROW 
DECLARE 
next_val NUMBER;
BEGIN
  --get new id from sequence per default
  SELECT NSM_USER_PREFERENCE_seq.NEXTVAL INTO next_val FROM DUAL;
  --:NEW.xxx Values supplied by insert or null
  IF (:NEW.upref_id IS NULL OR :New.upref_id < next_val) THEN
    --assign sequence value to table
    :NEW.upref_id:=next_val;
  ELSE
    --update sequence counter to fit last value
    WHILE (:NEW.upref_id > next_val)
    LOOP
      SELECT NSM_USER_PREFERENCE_seq.NEXTVAL INTO next_val FROM DUAL;
    END LOOP;
  END IF;
  --no internal exeption handling, all should match or handled by app
END;
/


/*          Initial data import              */
 
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_created,role_modified) VALUES ('1','icinga_user','The default representation of a ICINGA user','0',sysdate,sysdate);
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_created,role_modified) VALUES ('2','appkit_user','Appkit user test','0',sysdate,sysdate);
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_parent,role_created,role_modified) VALUES ('3','appkit_admin','AppKit admin','0','2',sysdate,sysdate);
INSERT INTO nsm_role (role_id,role_name,role_description,role_disabled,role_created,role_modified) VALUES ('4','guest','Unauthorized Guest','0',sysdate,sysdate);

INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('1','IcingaHostgroup','Limit data access to specific hostgroups','IcingaDataHostgroupPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('2','IcingaServicegroup','Limit data access to specific servicegroups','IcingaDataServicegroupPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('3','IcingaHostCustomVariablePair','Limit data access to specific custom variables','IcingaDataHostCustomVariablePrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('4','IcingaServiceCustomVariablePair','Limit data access to specific custom variables','IcingaDataServiceCustomVariablePrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('5','IcingaContactgroup','Limit data access to users contact group membership','IcingaDataContactgroupPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('6','IcingaCommandRo','Limit access to commands','IcingaDataCommandRoPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('7','appkit.access','Access to login-page (which, actually, means no access)','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('8','icinga.user','Access to icinga','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('9','appkit.admin.groups','Access to group related data (e.g. share cronks)','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('10','appkit.admin.users','Access to user related data (provider)','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('11','appkit.admin','Access to admin panel ','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('12','appkit.user.dummy','Basic right for users','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('13','appkit.api.access','Access to web-based api adapter','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('14','icinga.demoMode','Hide features like password reset which are not wanted in demo systems','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('15','icinga.cronk.category.admin','Enables category admin features','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('16','icinga.cronk.log','Enables icinga-log cronk','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('17','icinga.control.view','Allow user to view icinga status','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('18','icinga.control.admin','Allow user to administrate the icinga process','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('19','IcingaCommandRestrictions','Disable critical commands for this user','IcingaDataCommandRestrictionPrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('20','icinga.cronk.custom','Allow user to create and modify custom cronks','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('21','icinga.cronk.admin','Allow user to edit and delete all cronks','','credential');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('22','IcingaService','Limit data access to specific services','IcingaDataServicePrincipalTarget','icinga');
INSERT INTO nsm_target (target_id,target_name,target_description,target_class,target_type) VALUES ('23','IcingaHost','Limit data access to specific hosts','IcingaDataHostPrincipalTarget','icinga');

INSERT INTO nsm_user (user_id,user_account,user_name,user_firstname,user_lastname,user_password,user_salt,user_authsrc,user_email,user_disabled,user_created,user_modified) VALUES ('1','0','root','Enoch','Root','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','internal','root@localhost.local','0',sysdate,sysdate);

INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.10.0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

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

/* final commit */
commit;

/* final check */
select object_name,object_type,status  from user_objects where status !='VALID';

/* goodbye */
spool off;
exit;
