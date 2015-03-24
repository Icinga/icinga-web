/*
-- --------------------------------------------------------
-- create_oracle_sys.sql
-- Create icinga tablespace and user (SYS User part)
-- called and defines set from oracle.sql
--
-- Copyright (c) 2009-2015 Icinga Development Team (http://www.icinga.org)
--
-- works with Oracle10+ and sqlplus
-- for because of grants on v$ views this must run as sys 
-- --------------------------------------------------------
*/
-- -----------------------------------------
-- run with
-- # sqlplus "sys@INSTANCE as sysdba" @ create_oracle_sys.sql
-- -----------------------------------------
/*
filesystems to use for distributing index and data. In low frequency environments
this can be the same. trailing slash is mandantory
*/
DEFINE DATAFS=./
DEFINE IDXFS=./

/*
icinga web tablespaces and user must fit definitions in oracle.sql
*/
DEFINE DATATBS=ICINGAWEB_DATA1
DEFINE IDXTBS=ICINGAWEB_IDX1
DEFINE ICINGA_WEBUSER=icinga_web
DEFINE ICINGA_WEBPASSWORD=icinga_web

-- -----------------------------------------
-- set sqlplus parameter
-- -----------------------------------------
set sqlprompt "&&_USER@&&_CONNECT_IDENTIFIER SQL>"
set serveroutput on
set echo on
set feedback on

/* logging and error handling */
spool icinga_create_webuser.log
whenever sqlerror continue

-- -----------------------------------------
-- drop existing user if any
-- -----------------------------------------
prompt "nonexistent user" error on the next statement can be ignored
drop user &ICINGA_WEBUSER cascade;


-- -----------------------------------------
-- drop existing icinga tablespaces if any
-- -----------------------------------------
prompt "nonexistent tablespace" errors on the next statement can be ignored
 DROP TABLESPACE &&DATATBS including contents and datafiles;
 DROP TABLESPACE &&IDXTBS including contents and datafiles;

-- -----------------------------------------
-- Create new tablespaces
-- -----------------------------------------

/* create tablespaces */
CREATE TABLESPACE &DATATBS DATAFILE '&&DATAFS.&DATATBS..dbf'
        SIZE 50M AUTOEXTEND ON NEXT 50M MAXSIZE 2G
        LOGGING EXTENT MANAGEMENT LOCAL SEGMENT SPACE MANAGEMENT AUTO;
CREATE TABLESPACE &IDXTBS DATAFILE '&&IDXFS.&IDXTBS..dbf'
        SIZE 50M AUTOEXTEND ON NEXT 50M MAXSIZE 2G
        LOGGING EXTENT MANAGEMENT LOCAL SEGMENT SPACE MANAGEMENT AUTO;


-- -----------------------------------------
-- Create new user and grant rights on System and Quotas
-- -----------------------------------------

/* create user */
create user &ICINGA_WEBUSER identified by &ICINGA_WEBPASSWORD 
	default tablespace &DATATBS 
		temporary tablespace temp;

/* assing tablespace quotas */
alter user &&ICINGA_WEBUSER quota unlimited on &&DATATBS;
alter user &&ICINGA_WEBUSER quota unlimited on &&IDXTBS;

/* object rights */
grant connect,
        create table,
        create procedure,
        create trigger,
        create sequence,
        create synonym,
        create view,
        create type,
        alter session
to &&ICINGA_WEBUSER;
/* monitoring views,must be grantet from SYS  */
grant select on v_$session to &&ICINGA_WEBUSER;
grant select on v_$process to &&ICINGA_WEBUSER;
grant select on v_$sesstat to &&ICINGA_WEBUSER;
grant select on v_$mystat to &&ICINGA_WEBUSER;
grant select on v_$statname to &&ICINGA_WEBUSER;

prompt system prepared, run now create_icingaweb_objects_oracle.sql as &&ICINGA_WEBUSER
spool off;
exit;

