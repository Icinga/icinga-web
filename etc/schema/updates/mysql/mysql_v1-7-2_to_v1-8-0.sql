DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.0', NOW(), NOW());

INSERT INTO nsm_target
    (target_name,target_description,target_class,target_type) 
VALUES (
    'icinga.cronk.admin',
    'Allow user edit and delete all cronks',
    '',
    'credential'
);

ALTER TABLE cronk
    ADD COLUMN cronk_system tinyint(1) DEFAULT 0;

ALTER TABLE cronk_category
    ADD COLUMN cc_system tinyint(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS `cronk_principal_category` (
    `principal_id` int(11) NOT NULL DEFAULT '0',
    `category_id` int(11) NOT NULL DEFAULT '0',

    PRIMARY KEY (`principal_id`,`category_id`),

    KEY `cronk_principal_category_category_id_cronk_category_cc_id` (`category_id`),

    CONSTRAINT `cronk_principal_category_category_id_cronk_category_cc_id` 
        FOREIGN KEY (`category_id`)
        REFERENCES `cronk_category` (`cc_id`),

    CONSTRAINT `cronk_principal_category_principal_id_nsm_principal_principal_id`
        FOREIGN KEY (`principal_id`)
        REFERENCES `nsm_principal` (`principal_id`)
        
) ENGINE=InnoDB;

INSERT INTO `nsm_target`
    (`target_name`, `target_description`, `target_type`, `target_class`) 
    VALUES (
        'IcingaService',
        'Limit data access to specific services',
        'icinga',
        'IcingaDataServicePrincipalTarget'
);

INSERT INTO `nsm_target`
    (`target_name`, `target_description`, `target_type`, `target_class`) 
    VALUES (
        'IcingaHost', 
        'Limit data access to specific hosts',
        'icinga',
        'IcingaDataHostPrincipalTarget'
);

ALTER TABLE nsm_user
    CHANGE COLUMN user_authid user_authid varchar(512) NULL DEFAULT NULL;