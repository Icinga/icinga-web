\set icinga_web_owner 'icinga_web';

DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.0', NOW(), NOW());

ALTER TABLE cronk
    ADD COLUMN cronk_system boolean DEFAULT false;

ALTER TABLE cronk_category
    ADD COLUMN cc_system boolean DEFAULT false;

CREATE TABLE cronk_principal_category (
    principal_id integer NOT NULL,
    category_id integer NOT NULL
);

ALTER TABLE public.cronk_principal_category OWNER TO :icinga_web_owner;

ALTER TABLE ONLY cronk_principal_category
    ADD CONSTRAINT cronk_principal_category_pkey PRIMARY KEY (principal_id, category_id);

ALTER TABLE ONLY cronk_principal_category
    ADD CONSTRAINT cronk_principal_category_category_id_cronk_category_cc_id FOREIGN KEY (category_id) REFERENCES cronk_category(cc_id);

ALTER TABLE ONLY cronk_principal_category
    ADD CONSTRAINT cronk_principal_category_principal_id_nsm_principal_principal_i FOREIGN KEY (principal_id) REFERENCES nsm_principal(principal_id);

INSERT INTO nsm_target
    (target_name, target_description, target_type, target_class) 
    VALUES (
        'IcingaService',
        'Limit data access to specific services',
        'icinga',
        'IcingaDataServicePrincipalTarget'
);

INSERT INTO nsm_target
    (target_name, target_description, target_type, target_class) 
    VALUES (
        'IcingaHost', 
        'Limit data access to specific hosts',
        'icinga',
        'IcingaDataHostPrincipalTarget'
);

ALTER TABLE nsm_user
    ALTER COLUMN user_authid TYPE varchar(512);
