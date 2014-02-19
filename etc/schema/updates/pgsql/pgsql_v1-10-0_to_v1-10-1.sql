BEGIN;

CREATE UNIQUE INDEX session_id_idx ON nsm_session USING btree (session_id);

DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.10.1', NOW(), NOW());

END;
