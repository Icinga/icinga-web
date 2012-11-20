define IXTBS='ICINGA_IDX1';

DELETE FROM nsm_db_version;
INSERT INTO nsm_db_version VALUES ('1','icinga-web/v1.8.0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER TABLE cronk
    ADD (cronk_system NUMBER(3) DEFAULT 0);

ALTER TABLE cronk_category
    ADD (cc_system NUMBER(3) DEFAULT 0);

CREATE TABLE cronk_principal_category
  (
    principal_id NUMBER(10),
    category_id     NUMBER(10),
    constraint CPCAT_PK PRIMARY KEY(principal_id, category_id)
  )
  organization index
  tablespace &IXTBS;

ALTER TABLE cronk_principal_category ADD CONSTRAINT cpcat_pi_fk FOREIGN KEY (principal_id) 
  REFERENCES nsm_principal(principal_id);
  
ALTER TABLE cronk_principal_category ADD CONSTRAINT cpcat_ci_fk FOREIGN KEY (category_id) 
  REFERENCES cronk_category(cc_id);

INSERT INTO nsm_target
    (target_id, target_name, target_description, target_type, target_class) 
    VALUES (
        NSM_TARGET_seq.NEXTVAL,
        'IcingaService',
        'Limit data access to specific services',
        'icinga',
        'IcingaDataServicePrincipalTarget'
);

INSERT INTO nsm_target
    (target_id, target_name, target_description, target_type, target_class) 
    VALUES (
        NSM_TARGET_seq.NEXTVAL,
        'IcingaHost', 
        'Limit data access to specific hosts',
        'icinga',
        'IcingaDataHostPrincipalTarget' );

ALTER TABLE NSM_USER MODIFY ("USER_AUTHID" VARCHAR2(512 BYTE));
