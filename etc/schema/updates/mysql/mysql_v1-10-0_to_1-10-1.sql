ALTER TABLE nsm_session
    ADD UNIQUE INDEX session_id_idx (session_id);

-- Default version change
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.10.1', NOW(), NOW());

