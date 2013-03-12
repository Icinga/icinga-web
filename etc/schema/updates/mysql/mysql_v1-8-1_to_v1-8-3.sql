BEGIN;
SET autocommit = 0;

--
-- renaming duplicate appstate in user preference data by using the newest row
--
DROP TABLE IF EXISTS `nsm_user_preference_trans`;

-- copying current data to temp table
CREATE TEMPORARY TABLE nsm_user_preference_trans
SELECT * FROM nsm_user_preference;

-- locking
LOCK TABLES `nsm_user_preference` WRITE, `nsm_user_preference_trans` WRITE;

-- cleaning the table
DELETE FROM nsm_user_preference;

-- inserting deduplicated data
INSERT INTO nsm_user_preference
SELECT
    upref_id,
    upref_user_id,
    upref_val,
    upref_longval,
    upref_key,
    upref_created,
    upref_modified
FROM nsm_user_preference_trans
GROUP by upref_user_id, upref_key
HAVING MAX(upref_modified);

-- adding unique key for user_id and key of preference
ALTER TABLE nsm_user_preference ADD UNIQUE KEY upref_user_key_unique_idx (upref_user_id, upref_key);

-- clean up
DROP TABLE nsm_user_preference_trans;
UNLOCK TABLES;

-- update version info
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.3', NOW(), NOW());

COMMIT;
