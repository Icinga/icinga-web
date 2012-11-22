DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.1', NOW(), NOW());

UPDATE nsm_target SET target_class = 'IcingaDataCommandRestrictionPrincipalTarget' WHERE target_name = 'IcingaCommandRestrictions';

