\set icinga_web_owner 'icinga_web';

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: cronk; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE cronk (
    cronk_id integer NOT NULL,
    cronk_uid character varying(45),
    cronk_name character varying(45),
    cronk_description character varying(100),
    cronk_xml text,
    cronk_user_id integer,
    cronk_system boolean DEFAULT false,
    cronk_created timestamp without time zone NOT NULL,
    cronk_modified timestamp without time zone NOT NULL
);


ALTER TABLE public.cronk OWNER TO :icinga_web_owner;

--
-- Name: cronk_category; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE cronk_category (
    cc_id integer NOT NULL,
    cc_uid character varying(45) NOT NULL,
    cc_name character varying(45),
    cc_visible smallint DEFAULT 0,
    cc_position integer DEFAULT 0,
    cc_system boolean DEFAULT false,
    cc_created timestamp without time zone NOT NULL,
    cc_modified timestamp without time zone NOT NULL
);


ALTER TABLE public.cronk_category OWNER TO :icinga_web_owner;

--
-- Name: cronk_category_cc_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE cronk_category_cc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cronk_category_cc_id_seq OWNER TO :icinga_web_owner;

--
-- Name: cronk_category_cc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE cronk_category_cc_id_seq OWNED BY cronk_category.cc_id;


--
-- Name: cronk_category_cc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('cronk_category_cc_id_seq', 1, false);


--
-- Name: cronk_category_cronk; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE cronk_category_cronk (
    ccc_cc_id integer NOT NULL,
    ccc_cronk_id integer NOT NULL
);


ALTER TABLE public.cronk_category_cronk OWNER TO :icinga_web_owner;

--
-- Name: cronk_cronk_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE cronk_cronk_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cronk_cronk_id_seq OWNER TO :icinga_web_owner;

--
-- Name: cronk_cronk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE cronk_cronk_id_seq OWNED BY cronk.cronk_id;


--
-- Name: cronk_cronk_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('cronk_cronk_id_seq', 1, false);


--
-- Name: cronk_principal_category; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE cronk_principal_category (
    principal_id integer NOT NULL,
    category_id integer NOT NULL
);


ALTER TABLE public.cronk_principal_category OWNER TO :icinga_web_owner;

--
-- Name: cronk_principal_cronk; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE cronk_principal_cronk (
    cpc_principal_id integer NOT NULL,
    cpc_cronk_id integer NOT NULL
);


ALTER TABLE public.cronk_principal_cronk OWNER TO :icinga_web_owner;

--
-- Name: nsm_db_version; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_db_version (
    id integer NOT NULL,
    version character varying(32) NOT NULL,
    modified timestamp without time zone NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.nsm_db_version OWNER TO :icinga_web_owner;

--
-- Name: nsm_log; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_log (
    log_id integer NOT NULL,
    log_level integer NOT NULL,
    log_message text NOT NULL,
    log_created timestamp without time zone NOT NULL,
    log_modified timestamp without time zone NOT NULL
);


ALTER TABLE public.nsm_log OWNER TO :icinga_web_owner;

--
-- Name: nsm_log_log_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_log_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_log_log_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_log_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_log_log_id_seq OWNED BY nsm_log.log_id;


--
-- Name: nsm_log_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_log_log_id_seq', 1, false);


--
-- Name: nsm_principal; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_principal (
    principal_id integer NOT NULL,
    principal_user_id integer,
    principal_role_id integer,
    principal_type character varying(4) NOT NULL,
    principal_disabled smallint DEFAULT 0
);


ALTER TABLE public.nsm_principal OWNER TO :icinga_web_owner;

--
-- Name: nsm_principal_principal_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_principal_principal_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_principal_principal_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_principal_principal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_principal_principal_id_seq OWNED BY nsm_principal.principal_id;


--
-- Name: nsm_principal_principal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_principal_principal_id_seq', 6, true);


--
-- Name: nsm_principal_target; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_principal_target (
    pt_id integer NOT NULL,
    pt_principal_id integer NOT NULL,
    pt_target_id integer NOT NULL
);


ALTER TABLE public.nsm_principal_target OWNER TO :icinga_web_owner;

--
-- Name: nsm_principal_target_pt_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_principal_target_pt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_principal_target_pt_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_principal_target_pt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_principal_target_pt_id_seq OWNED BY nsm_principal_target.pt_id;


--
-- Name: nsm_principal_target_pt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_principal_target_pt_id_seq', 14, true);


--
-- Name: nsm_role; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_role (
    role_id integer NOT NULL,
    role_name character varying(40) NOT NULL,
    role_description character varying(255),
    role_disabled smallint DEFAULT 0 NOT NULL,
    role_created timestamp without time zone NOT NULL,
    role_modified timestamp without time zone NOT NULL,
    role_parent integer
);


ALTER TABLE public.nsm_role OWNER TO :icinga_web_owner;

--
-- Name: nsm_role_role_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_role_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_role_role_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_role_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_role_role_id_seq OWNED BY nsm_role.role_id;


--
-- Name: nsm_role_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_role_role_id_seq', 5, true);


--
-- Name: nsm_session; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_session (
    session_entry_id integer NOT NULL,
    session_id character varying(255) NOT NULL,
    session_name character varying(255) NOT NULL,
    session_data text NOT NULL,
    session_checksum character varying(255) NOT NULL,
    session_created timestamp without time zone NOT NULL,
    session_modified timestamp without time zone NOT NULL
);


ALTER TABLE public.nsm_session OWNER TO :icinga_web_owner;

--
-- Name: nsm_session_session_entry_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_session_session_entry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_session_session_entry_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_session_session_entry_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_session_session_entry_id_seq OWNED BY nsm_session.session_entry_id;


--
-- Name: nsm_session_session_entry_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_session_session_entry_id_seq', 1, false);


--
-- Name: nsm_target; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_target (
    target_id integer NOT NULL,
    target_name character varying(45) NOT NULL,
    target_description character varying(100),
    target_class character varying(80),
    target_type character varying(45) NOT NULL
);


ALTER TABLE public.nsm_target OWNER TO :icinga_web_owner;

--
-- Name: nsm_target_target_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_target_target_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_target_target_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_target_target_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_target_target_id_seq OWNED BY nsm_target.target_id;


--
-- Name: nsm_target_target_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_target_target_id_seq', 24, true);


--
-- Name: nsm_target_value; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_target_value (
    tv_pt_id integer NOT NULL,
    tv_key character varying(45) NOT NULL,
    tv_val character varying(45) NOT NULL
);


ALTER TABLE public.nsm_target_value OWNER TO :icinga_web_owner;

--
-- Name: nsm_user; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_user (
    user_id integer NOT NULL,
    user_account integer DEFAULT 0 NOT NULL,
    user_name character varying(127) NOT NULL,
    user_lastname character varying(40) NOT NULL,
    user_firstname character varying(40) NOT NULL,
    user_password character varying(64) NOT NULL,
    user_salt character varying(64) NOT NULL,
    user_authsrc character varying(45) DEFAULT 'internal'::character varying NOT NULL,
    user_authid character varying(512),
    user_authkey character varying(64),
    user_email character varying(254) NOT NULL,
    user_description character varying(255),
    user_disabled smallint DEFAULT 1 NOT NULL,
    user_created timestamp without time zone NOT NULL,
    user_modified timestamp without time zone NOT NULL,
    user_last_login timestamp without time zone
);


ALTER TABLE public.nsm_user OWNER TO :icinga_web_owner;

--
-- Name: nsm_user_preference; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_user_preference (
    upref_id integer NOT NULL,
    upref_user_id integer NOT NULL,
    upref_val character varying(100),
    upref_longval text,
    upref_key character varying(50) NOT NULL,
    upref_created timestamp without time zone NOT NULL,
    upref_modified timestamp without time zone NOT NULL
);


ALTER TABLE public.nsm_user_preference OWNER TO :icinga_web_owner;

--
-- Name: nsm_user_preference_upref_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_user_preference_upref_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_user_preference_upref_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_user_preference_upref_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_user_preference_upref_id_seq OWNED BY nsm_user_preference.upref_id;


--
-- Name: nsm_user_preference_upref_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_user_preference_upref_id_seq', 1, false);


--
-- Name: nsm_user_role; Type: TABLE; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE TABLE nsm_user_role (
    usro_user_id integer NOT NULL,
    usro_role_id integer NOT NULL
);


ALTER TABLE public.nsm_user_role OWNER TO :icinga_web_owner;

--
-- Name: nsm_user_user_id_seq; Type: SEQUENCE; Schema: public; Owner: icinga_web
--

CREATE SEQUENCE nsm_user_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nsm_user_user_id_seq OWNER TO :icinga_web_owner;

--
-- Name: nsm_user_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: icinga_web
--

ALTER SEQUENCE nsm_user_user_id_seq OWNED BY nsm_user.user_id;


--
-- Name: nsm_user_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: icinga_web
--

SELECT pg_catalog.setval('nsm_user_user_id_seq', 2, true);


--
-- Name: cronk_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk ALTER COLUMN cronk_id SET DEFAULT nextval('cronk_cronk_id_seq'::regclass);


--
-- Name: cc_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_category ALTER COLUMN cc_id SET DEFAULT nextval('cronk_category_cc_id_seq'::regclass);


--
-- Name: log_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_log ALTER COLUMN log_id SET DEFAULT nextval('nsm_log_log_id_seq'::regclass);


--
-- Name: principal_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_principal ALTER COLUMN principal_id SET DEFAULT nextval('nsm_principal_principal_id_seq'::regclass);


--
-- Name: pt_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_principal_target ALTER COLUMN pt_id SET DEFAULT nextval('nsm_principal_target_pt_id_seq'::regclass);


--
-- Name: role_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_role ALTER COLUMN role_id SET DEFAULT nextval('nsm_role_role_id_seq'::regclass);


--
-- Name: session_entry_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_session ALTER COLUMN session_entry_id SET DEFAULT nextval('nsm_session_session_entry_id_seq'::regclass);


--
-- Name: target_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_target ALTER COLUMN target_id SET DEFAULT nextval('nsm_target_target_id_seq'::regclass);


--
-- Name: user_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_user ALTER COLUMN user_id SET DEFAULT nextval('nsm_user_user_id_seq'::regclass);


--
-- Name: upref_id; Type: DEFAULT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_user_preference ALTER COLUMN upref_id SET DEFAULT nextval('nsm_user_preference_upref_id_seq'::regclass);


--
-- Data for Name: cronk; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY cronk (cronk_id, cronk_uid, cronk_name, cronk_description, cronk_xml, cronk_user_id, cronk_system, cronk_created, cronk_modified) FROM stdin;
\.


--
-- Data for Name: cronk_category; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY cronk_category (cc_id, cc_uid, cc_name, cc_visible, cc_position, cc_system, cc_created, cc_modified) FROM stdin;
\.


--
-- Data for Name: cronk_category_cronk; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY cronk_category_cronk (ccc_cc_id, ccc_cronk_id) FROM stdin;
\.


--
-- Data for Name: cronk_principal_category; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY cronk_principal_category (principal_id, category_id) FROM stdin;
\.


--
-- Data for Name: cronk_principal_cronk; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY cronk_principal_cronk (cpc_principal_id, cpc_cronk_id) FROM stdin;
\.


--
-- Data for Name: nsm_db_version; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_db_version (id, version, modified, created) FROM stdin;
1	icinga-web/v1.10.0	2013-10-24 00:00:00	2013-10-24 00:00:00
\.


--
-- Data for Name: nsm_log; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_log (log_id, log_level, log_message, log_created, log_modified) FROM stdin;
\.


--
-- Data for Name: nsm_principal; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_principal (principal_id, principal_user_id, principal_role_id, principal_type, principal_disabled) FROM stdin;
1	1	\N	user	0
2	\N	2	role	0
3	\N	3	role	0
4	\N	1	role	0
5	\N	4	role	0
\.


--
-- Data for Name: nsm_principal_target; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_principal_target (pt_id, pt_principal_id, pt_target_id) FROM stdin;
1	2	8
2	2	13
3	3	9
4	3	10
5	3	11
6	4	8
7	5	7
8	3	15
9	3	16
10	3	17
11	3	18
12	4	20
13	3	21
\.


--
-- Data for Name: nsm_role; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_role (role_id, role_name, role_description, role_disabled, role_created, role_modified, role_parent) FROM stdin;
1	icinga_user	The default representation of a ICINGA user	0	2012-10-11 11:13:08	2012-10-11 11:13:08	\N
2	appkit_user	Appkit user test	0	2012-10-11 11:13:08	2012-10-11 11:13:08	\N
3	appkit_admin	AppKit admin	0	2012-10-11 11:13:08	2012-10-11 11:13:08	2
4	guest	Unauthorized Guest	0	2012-10-11 11:13:08	2012-10-11 11:13:08	\N
\.


--
-- Data for Name: nsm_session; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_session (session_entry_id, session_id, session_name, session_data, session_checksum, session_created, session_modified) FROM stdin;
\.


--
-- Data for Name: nsm_target; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_target (target_id, target_name, target_description, target_class, target_type) FROM stdin;
1	IcingaHostgroup	Limit data access to specific hostgroups	IcingaDataHostgroupPrincipalTarget	icinga
2	IcingaServicegroup	Limit data access to specific servicegroups	IcingaDataServicegroupPrincipalTarget	icinga
3	IcingaHostCustomVariablePair	Limit data access to specific custom variables	IcingaDataHostCustomVariablePrincipalTarget	icinga
4	IcingaServiceCustomVariablePair	Limit data access to specific custom variables	IcingaDataServiceCustomVariablePrincipalTarget	icinga
5	IcingaContactgroup	Limit data access to users contact group membership	IcingaDataContactgroupPrincipalTarget	icinga
6	IcingaCommandRo	Limit access to commands	IcingaDataCommandRoPrincipalTarget	icinga
7	appkit.access	Access to login-page (which, actually, means no access)		credential
8	icinga.user	Access to icinga		credential
9	appkit.admin.groups	Access to group related data (e.g. share cronks)		credential
10	appkit.admin.users	Access to user related data (provider)		credential
11	appkit.admin	Access to admin panel 		credential
12	appkit.user.dummy	Basic right for users		credential
13	appkit.api.access	Access to web-based api adapter		credential
14	icinga.demoMode	Hide features like password reset which are not wanted in demo systems		credential
15	icinga.cronk.category.admin	Enables category admin features		credential
16	icinga.cronk.log	Allow user to view icinga-log		credential
17	icinga.control.view	Allow user to view icinga status		credential
18	icinga.control.admin	Allow user to administrate the icinga process		credential
19	IcingaCommandRestrictions	Disable critical commands for this user	IcingaDataCommandRestrictionPrincipalTarget	icinga
20	icinga.cronk.custom	Allow user to create and modify custom cronks	\N	credential
21	icinga.cronk.admin	Allow user to edit and delete all cronks		credential
22	IcingaService	Limit data access to specific services	IcingaDataServicePrincipalTarget	icinga
23	IcingaHost	Limit data access to specific hosts	IcingaDataHostPrincipalTarget	icinga
\.


--
-- Data for Name: nsm_target_value; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_target_value (tv_pt_id, tv_key, tv_val) FROM stdin;
\.


--
-- Data for Name: nsm_user; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_user (user_id, user_account, user_name, user_lastname, user_firstname, user_password, user_salt, user_authsrc, user_authid, user_authkey, user_email, user_disabled, user_created, user_modified) FROM stdin;
1	0	root	Root	Enoch	42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1	0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd	internal	\N	\N	root@localhost.local	0	2012-10-11 11:13:07	2012-10-11 11:13:07
\.


--
-- Data for Name: nsm_user_preference; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_user_preference (upref_id, upref_user_id, upref_val, upref_longval, upref_key, upref_created, upref_modified) FROM stdin;
\.


--
-- Data for Name: nsm_user_role; Type: TABLE DATA; Schema: public; Owner: icinga_web
--

COPY nsm_user_role (usro_user_id, usro_role_id) FROM stdin;
1	1
1	2
1	3
\.


--
-- Name: cronk_category_cronk_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY cronk_category_cronk
    ADD CONSTRAINT cronk_category_cronk_pkey PRIMARY KEY (ccc_cc_id, ccc_cronk_id);


--
-- Name: cronk_category_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY cronk_category
    ADD CONSTRAINT cronk_category_pkey PRIMARY KEY (cc_id);


--
-- Name: cronk_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY cronk
    ADD CONSTRAINT cronk_pkey PRIMARY KEY (cronk_id);


--
-- Name: cronk_principal_category_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY cronk_principal_category
    ADD CONSTRAINT cronk_principal_category_pkey PRIMARY KEY (principal_id, category_id);


--
-- Name: cronk_principal_cronk_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY cronk_principal_cronk
    ADD CONSTRAINT cronk_principal_cronk_pkey PRIMARY KEY (cpc_principal_id, cpc_cronk_id);


--
-- Name: nsm_db_version_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_db_version
    ADD CONSTRAINT nsm_db_version_pkey PRIMARY KEY (id);


--
-- Name: nsm_log_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_log
    ADD CONSTRAINT nsm_log_pkey PRIMARY KEY (log_id);


--
-- Name: nsm_principal_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_principal
    ADD CONSTRAINT nsm_principal_pkey PRIMARY KEY (principal_id);


--
-- Name: nsm_principal_target_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_principal_target
    ADD CONSTRAINT nsm_principal_target_pkey PRIMARY KEY (pt_id);


--
-- Name: nsm_role_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_role
    ADD CONSTRAINT nsm_role_pkey PRIMARY KEY (role_id);


--
-- Name: nsm_session_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_session
    ADD CONSTRAINT nsm_session_pkey PRIMARY KEY (session_entry_id);


--
-- Name: nsm_target_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_target
    ADD CONSTRAINT nsm_target_pkey PRIMARY KEY (target_id);


--
-- Name: nsm_target_value_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_target_value
    ADD CONSTRAINT nsm_target_value_pkey PRIMARY KEY (tv_pt_id, tv_key);


--
-- Name: nsm_user_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_user
    ADD CONSTRAINT nsm_user_pkey PRIMARY KEY (user_id);


--
-- Name: nsm_user_preference_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_user_preference
    ADD CONSTRAINT nsm_user_preference_pkey PRIMARY KEY (upref_id);


--
-- Name: nsm_user_role_pkey; Type: CONSTRAINT; Schema: public; Owner: icinga_web; Tablespace: 
--

ALTER TABLE ONLY nsm_user_role
    ADD CONSTRAINT nsm_user_role_pkey PRIMARY KEY (usro_user_id, usro_role_id);


--
-- Name: cc_uid_unique; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE UNIQUE INDEX cc_uid_unique ON cronk_category USING btree (cc_uid);


--
-- Name: cronk_uid_unique; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE UNIQUE INDEX cronk_uid_unique ON cronk USING btree (cronk_uid);


--
-- Name: nsm_user_role_ix; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX nsm_user_role_ix ON nsm_user_role USING btree (usro_role_id);


--
-- Name: principal_collection_idx; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX principal_collection_idx ON nsm_principal USING btree (principal_user_id, principal_role_id, principal_type);


--
-- Name: principal_role_id_ix; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX principal_role_id_ix ON nsm_user_preference USING btree (upref_user_id);


--
-- Name: pt_principal_id_ix; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX pt_principal_id_ix ON nsm_principal_target USING btree (pt_principal_id);


--
-- Name: pt_target_id_ix; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX pt_target_id_ix ON nsm_principal_target USING btree (pt_target_id);


--
-- Name: upref_search_key_idx; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX upref_search_key_idx ON nsm_user_preference USING btree (upref_key);


--
-- Name: upref_user_key_unique_idx; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE UNIQUE INDEX upref_user_key_unique_idx ON nsm_user_preference USING btree (upref_user_id, upref_key);


--
-- Name: user_name_unique; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE UNIQUE INDEX user_name_unique ON nsm_user USING btree (user_name);


--
-- Name: user_search; Type: INDEX; Schema: public; Owner: icinga_web; Tablespace: 
--

CREATE INDEX user_search ON nsm_user USING btree (user_name, user_authsrc, user_authid, user_disabled);


--
-- Name: ccnp; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_principal_cronk
    ADD CONSTRAINT ccnp FOREIGN KEY (cpc_principal_id) REFERENCES nsm_principal(principal_id);


--
-- Name: cronk_category_cronk_ccc_cc_id_cronk_category_cc_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_category_cronk
    ADD CONSTRAINT cronk_category_cronk_ccc_cc_id_cronk_category_cc_id FOREIGN KEY (ccc_cc_id) REFERENCES cronk_category(cc_id);


--
-- Name: cronk_category_cronk_ccc_cronk_id_cronk_cronk_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_category_cronk
    ADD CONSTRAINT cronk_category_cronk_ccc_cronk_id_cronk_cronk_id FOREIGN KEY (ccc_cronk_id) REFERENCES cronk(cronk_id);


--
-- Name: cronk_cronk_user_id_nsm_user_user_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk
    ADD CONSTRAINT cronk_cronk_user_id_nsm_user_user_id FOREIGN KEY (cronk_user_id) REFERENCES nsm_user(user_id);


--
-- Name: cronk_principal_category_category_id_cronk_category_cc_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_principal_category
    ADD CONSTRAINT cronk_principal_category_category_id_cronk_category_cc_id FOREIGN KEY (category_id) REFERENCES cronk_category(cc_id);


--
-- Name: cronk_principal_category_principal_id_nsm_principal_principal_i; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_principal_category
    ADD CONSTRAINT cronk_principal_category_principal_id_nsm_principal_principal_i FOREIGN KEY (principal_id) REFERENCES nsm_principal(principal_id);


--
-- Name: cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY cronk_principal_cronk
    ADD CONSTRAINT cronk_principal_cronk_cpc_cronk_id_cronk_cronk_id FOREIGN KEY (cpc_cronk_id) REFERENCES cronk(cronk_id);


--
-- Name: nsm_principal_principal_role_id_nsm_role_role_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_principal
    ADD CONSTRAINT nsm_principal_principal_role_id_nsm_role_role_id FOREIGN KEY (principal_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_principal_principal_user_id_nsm_user_user_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_principal
    ADD CONSTRAINT nsm_principal_principal_user_id_nsm_user_user_id FOREIGN KEY (principal_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_principal_target_pt_principal_id_nsm_principal_principal_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_principal_target
    ADD CONSTRAINT nsm_principal_target_pt_principal_id_nsm_principal_principal_id FOREIGN KEY (pt_principal_id) REFERENCES nsm_principal(principal_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_principal_target_pt_target_id_nsm_target_target_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_principal_target
    ADD CONSTRAINT nsm_principal_target_pt_target_id_nsm_target_target_id FOREIGN KEY (pt_target_id) REFERENCES nsm_target(target_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_role_role_parent_nsm_role_role_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_role
    ADD CONSTRAINT nsm_role_role_parent_nsm_role_role_id FOREIGN KEY (role_parent) REFERENCES nsm_role(role_id);


--
-- Name: nsm_target_value_tv_pt_id_nsm_principal_target_pt_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_target_value
    ADD CONSTRAINT nsm_target_value_tv_pt_id_nsm_principal_target_pt_id FOREIGN KEY (tv_pt_id) REFERENCES nsm_principal_target(pt_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_user_preference_upref_user_id_nsm_user_user_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_user_preference
    ADD CONSTRAINT nsm_user_preference_upref_user_id_nsm_user_user_id FOREIGN KEY (upref_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_user_role_usro_role_id_nsm_role_role_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_user_role
    ADD CONSTRAINT nsm_user_role_usro_role_id_nsm_role_role_id FOREIGN KEY (usro_role_id) REFERENCES nsm_role(role_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: nsm_user_role_usro_user_id_nsm_user_user_id; Type: FK CONSTRAINT; Schema: public; Owner: icinga_web
--

ALTER TABLE ONLY nsm_user_role
    ADD CONSTRAINT nsm_user_role_usro_user_id_nsm_user_user_id FOREIGN KEY (usro_user_id) REFERENCES nsm_user(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


-- Unique key for nsm_target / target_name
CREATE UNIQUE INDEX target_key_unique_target_name_idx ON nsm_target USING btree (target_name);

-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

