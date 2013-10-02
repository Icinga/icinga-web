--error handler
whenever sqlerror exit failure

-- Default version bump
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.10.0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

alter table nsm_user ADD (user_last_login DATE);

--final commit
commit;

