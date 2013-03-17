/* update script for icinga-web on oracle version v1.8.3 */
set echo on
set feedback on
/* logging */
spool oracle_v1-8-0_to_v1-8-3.log

/* define index tablespace. change this for your needs */
define IXTBS='ICINGAWEB_IDX1';

/* 
drop existing constraint if any 
ignore ORA-02443 nonexistent constraint warning!
*/
alter table nsm_user_preference drop constraint nsm_user_pref_userid_key_uq drop index;

/* from now leave on error */
whenever sqlerror exit failure


/* delete duplicates ifirst if any and than add unique key for preference user_id and key  #3870 */
delete from nsm_user_preference ud
where 
  upref_modified <
  (select max(upref_modified) from nsm_user_preference ur
    where ud.upref_user_id=ur.upref_user_id
      and ud.upref_key=ur.upref_key);
alter table nsm_user_preference add constraint nsm_user_pref_userid_key_uq UNIQUE (upref_user_id, upref_key)
  using index tablespace &IXTBS;


/* update version info */
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.3', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

/* commit all changes as single transaction, this will not be reached on error */
commit;

/*  done */
spool off;
exit;

