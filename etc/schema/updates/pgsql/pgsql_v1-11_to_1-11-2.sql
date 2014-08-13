BEGIN;

DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.11.2', NOW(), NOW());

END;
