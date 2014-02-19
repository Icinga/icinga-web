--error handler
whenever sqlerror exit failure

alter table nsm_session add constraint nsm_session_id_uq UNIQUE (session_id)
	using index tablespace &IXTBS;

-- Default version bump
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.10.1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

--final commit
commit;

