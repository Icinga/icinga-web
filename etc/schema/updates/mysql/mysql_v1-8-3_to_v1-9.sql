-- Default version change
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.9.0', NOW(), NOW());

-- Add user description attribute (#3923)
ALTER TABLE nsm_user
    ADD COLUMN user_description VARCHAR(255);

-- Extend cronk_xml columns for big Cronks (#3951)
ALTER TABLE cronk MODIFY COLUMN cronk_xml LONGTEXT;

