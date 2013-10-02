DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.10.0', NOW(), NOW());

ALTER TABLE nsm_user
    ADD COLUMN user_last_login timestamp without time zone;
