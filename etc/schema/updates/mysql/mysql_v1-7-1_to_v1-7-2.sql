DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.7.2', NOW(), NOW());

INSERT INTO nsm_target
    (target_name,target_description,target_class,target_type) 
VALUES (
    'icinga.cronk.admin',
    'Allow user edit and delete all cronks',
    '',
    'credential'
);
