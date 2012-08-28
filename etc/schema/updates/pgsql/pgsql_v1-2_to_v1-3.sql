CREATE SEQUENCE cronk_category_cc_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cronk_cronk_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE TABLE cronk (
	cronk_id integer DEFAULT nextval('cronk_cronk_id_seq'::regclass) NOT NULL,
	cronk_uid character varying(45),
	cronk_name character varying(45),
	cronk_description character varying(100),
	cronk_xml text,
	cronk_user_id integer,
	cronk_created timestamp without time zone NOT NULL,
	cronk_modified timestamp without time zone NOT NULL
);

CREATE TABLE cronk_category (
	cc_id integer DEFAULT nextval('cronk_category_cc_id_seq'::regclass) NOT NULL,
	cc_uid character varying(45) NOT NULL,
	cc_name character varying(45),
	cc_visible smallint DEFAULT 0,
	cc_position integer DEFAULT 0,
	cc_created timestamp without time zone NOT NULL,
	cc_modified timestamp without time zone NOT NULL
);

CREATE TABLE cronk_category_cronk (
	ccc_cc_id integer NOT NULL,
	ccc_cronk_id integer NOT NULL
);

CREATE TABLE cronk_principal_cronk (
	cpc_principal_id integer NOT NULL,
	cpc_cronk_id integer NOT NULL
);

ALTER TABLE cronk
	ADD CONSTRAINT cronk_pkey PRIMARY KEY (cronk_id);

ALTER TABLE cronk_category
	ADD CONSTRAINT cronk_category_pkey PRIMARY KEY (cc_id);

ALTER TABLE cronk_category_cronk
	ADD CONSTRAINT cronk_category_cronk_pkey PRIMARY KEY (ccc_cc_id, ccc_cronk_id);

ALTER TABLE cronk_principal_cronk
	ADD CONSTRAINT cronk_principal_cronk_pkey PRIMARY KEY (cpc_principal_id, cpc_cronk_id);

ALTER TABLE cronk
	ADD CONSTRAINT cronk_cronk_user_id_nsm_user_user_id FOREIGN KEY (cronk_user_id) REFERENCES nsm_user(user_id);

ALTER TABLE cronk_category_cronk
	ADD CONSTRAINT cronk_category_cronk_ccc_cc_id_cronk_category_cc_id FOREIGN KEY (ccc_cc_id) REFERENCES cronk_category(cc_id);

ALTER TABLE cronk_category_cronk
	ADD CONSTRAINT cronk_category_cronk_ccc_cronk_id_cronk_cronk_id FOREIGN KEY (ccc_cronk_id) REFERENCES cronk(cronk_id);

ALTER TABLE cronk_principal_cronk
	ADD CONSTRAINT ccnp FOREIGN KEY (cpc_principal_id) REFERENCES nsm_principal(principal_id);

ALTER TABLE cronk_principal_cronk
	ADD CONSTRAINT cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id FOREIGN KEY (cpc_cronk_id) REFERENCES cronk(cronk_id);

CREATE UNIQUE INDEX cronk_uid_unique ON cronk USING btree (cronk_uid);

CREATE UNIQUE INDEX cc_uid_unique ON cronk_category USING btree (cc_uid);

-- Adding new credential and add them to appkit_admin

INSERT INTO nsm_target (target_name,target_description,target_class,target_type) VALUES ('icinga.control.view','Allow user to view icinga status','','credential')
INSERT INTO nsm_target (target_name,target_description,target_class,target_type) VALUES ('icinga.control.admin','Allow user to administrate the icinga process','','credential');
INSERT INTO nsm_target (target_name, target_description, target_type) VALUES ('icinga.cronk.category.admin', 'Enables category admin feature', 'credential');

INSERT INTO nsm_principal_target (pt_principal_id, pt_target_id) VALUES ('3', currval('nsm_target_target_id_seq'));

