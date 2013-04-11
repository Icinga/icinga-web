DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.9.0', NOW(), NOW());

-- User description attribute (#3923)
ALTER TABLE nsm_user
  ADD COLUMN user_description character varying(255);

-- Add unique constrain for target_name/NsmTarget (#3915)
CREATE UNIQUE INDEX target_key_unique_target_name_idx ON nsm_target USING btree (target_name);