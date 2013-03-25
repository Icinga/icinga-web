BEGIN;

--
-- renaming duplicate appstate in user preference data by using the newest row
--
DROP TABLE IF EXISTS nsm_user_preference_trans;

-- copying current data to temp table
CREATE TEMPORARY TABLE nsm_user_preference_trans AS
SELECT * FROM nsm_user_preference;

-- locking
LOCK TABLE nsm_user_preference, nsm_user_preference_trans IN ACCESS EXCLUSIVE MODE;

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
FROM nsm_user_preference_trans t
WHERE upref_id = (
    SELECT MAX(upref_id)
    FROM nsm_user_preference_trans t2
    WHERE t.upref_key = t2.upref_key
      and t.upref_user_id = t2.upref_user_id
);

-- adding unique key for user_id and key of preference
CREATE UNIQUE INDEX upref_user_key_unique_idx ON nsm_user_preference USING btree (upref_user_id, upref_key);

-- clean up
DROP TABLE nsm_user_preference_trans;

-- update version info
DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.3', NOW(), NOW());

COMMIT;
