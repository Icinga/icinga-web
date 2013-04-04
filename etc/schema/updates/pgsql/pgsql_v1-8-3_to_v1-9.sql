DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.9.0', NOW(), NOW());

-- User description attribute (#3923)
ALTER TABLE nsm_user
  ADD COLUMN user_description character varying(255);