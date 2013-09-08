--error handler
whenever sqlerror exit failure

-- User description attribute (#3923)
ALTER TABLE nsm_user
    ADD (user_description VARCHAR2(255));

-- Add unique constrain for target_name/NsmTarget (#3915)
ALTER TABLE
  nsm_target
  add constraint target_key_unq_tgt_n_uq UNIQUE (target_name);

-- Default version bump
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.9.0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

--final commit 
commit;

