#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

# Which version of AC
AC_PREREQ(2.61)

# Init project
AC_INIT(icinga-web, [1.0.1], [dev.icinga.org])

RELEASE_DATE="2010-06-30"

AC_PREFIX_DEFAULT(/usr/local/icinga-web)

# Checks for programs.
AC_PROG_INSTALL
AC_PROG_GREP
AC_PROG_SED

# Extract version parts
ACICINGA_EXTRACT_VERSION([VERSION_MAJOR],[VERSION_MINOR],[VERSION_PATCH],[VERSION_EXTEN])

AC_SUBST(VERSION_MAJOR)
AC_SUBST(VERSION_MINOR)
AC_SUBST(VERSION_PATCH)
AC_SUBST(VERSION_EXTEN)
AC_SUBST(RELEASE_DATE)

# Check for php
AC_ARG_VAR([PHP],[php cli binary])
ACICINGA_CHECK_BIN([PHP], [php])

# Check for mysql
AC_ARG_VAR([MYSQL],[mysql client binary])
ACICINGA_CHECK_BIN([MYSQL], [mysql])

# Check for phing
AC_ARG_VAR([PHING],[phing build tool])
ACICINGA_CHECK_BIN([PHING], [phing])

# Checks for libraries.

# Checks for header files.

# Checks for typedefs, structures, and compiler characteristics.

# Checks for library functions.

# Define install options

# Users for webfiles
AC_ARG_WITH([web_user],
	AS_HELP_STRING([--with-web-user=USER], [username for web writable files (default www-data)]),
	web_user=$withval,
	ACICINGA_USER_GUESS([www wwwrun www-data apache httpd nobody],[web_user],[www-data])
)

AC_ARG_WITH([web_group],
	AS_HELP_STRING([--with-web-group=GROUP], [group for web writable files (default www-data)]),
	web_group=$withval,
	ACICINGA_GROUP_GUESS([www www-data apache httpd nogroup nobody],[web_group], [www-data])
)

AC_ARG_WITH([web_path],
	AS_HELP_STRING([--with-web-path=PATH], [web sub path (default /icinga-web)]),
	web_path=$withval,
	web_path=/icinga-web
)

AC_ARG_WITH([web_apache_path],
	AS_HELP_STRING([--with-web-apache-path=PATH], [Include folder apache2 (default /etc/apache2/conf.d]),
	web_apache_path=$withval,
	web_apache_path=/etc/apache2/conf.d
)

AC_ARG_WITH([bin_user],
	AS_HELP_STRING([--with-bin-user=USER], [user for all other files (default root)]),
	bin_user=$withval,
	bin_user=root
)

AC_ARG_WITH([bin_group],
	AS_HELP_STRING([--with-bin-group=GROUP], [group for all other files (default bin)]),
	bin_group=$withval,
	bin_group=bin
)

# Api related
AC_ARG_WITH([icinga_api],
	AS_HELP_STRING([--with-icinga-api=PATH], [Path to icinga-api (default /usr/local/icinga/share/icinga-api)]),
	icinga_api=$withval,
	icinga_api=/usr/local/icinga/share/icinga-api
)

ACICINGA_CHECK_API([$icinga_api])

# Web database switches
AC_ARG_WITH([db_type],
	AS_HELP_STRING([--with-db-type=DBTYPE], [Type of dbms (default mysql)]),
	db_type=$withval,
	db_type=mysql
)

AC_ARG_WITH([db_host],
	AS_HELP_STRING([--with-db-host=HOST], [Host of dbms (default 127.0.0.1)]),
	db_host=$withval,
	db_host=127.0.0.1
)

AC_ARG_WITH([db_port],
	AS_HELP_STRING([--with-db-port=PORT], [Port of the dbms (default 3306)]),
	db_port=$withval,
	db_port=3306
)

AC_ARG_WITH([db_name],
	AS_HELP_STRING([--with-db-name=DBNAME], [DB name for icinga (default icinga_web)]),
	db_name=$withval,
	db_name=icinga_web
)

AC_ARG_WITH([db_user],
	AS_HELP_STRING([--with-db-user=USER], [DB user for icinga (default icinga_web)]),
	db_user=$withval,
	db_user=icinga_web
)

AC_ARG_WITH([db_pass],
	AS_HELP_STRING([--with-db-pass=PASSWD], [DB pass for icinga (default icinga_web)]),
	db_pass=$withval,
	db_pass=icinga_web
)

# Configure the icinga-api

AC_ARG_WITH([api_type],
	AS_HELP_STRING([--with-api-type=APICON], [API type (default CONNECTION_IDO)]),
	api_type=$withval,
	api_type=CONNECTION_IDO
)

AC_ARG_WITH([api_subtype],
	AS_HELP_STRING([--with-api-subtype=TYPE], [DB driver or network connection]),
	api_subtype=$withval,
	api_subtype=mysql
)

AC_ARG_WITH([api_host],
	AS_HELP_STRING([--with-api-host=HOST], [Host to connect (DB or other) (default localhost)]),
	api_host=$withval,
	api_host=localhost
)

AC_ARG_WITH([api_port],
	AS_HELP_STRING([--with-api-port=PORT], [Port for connection (default 3306)]),
	api_port=$withval,
	api_port=3306
)

AC_ARG_WITH([api_socket],
	AS_HELP_STRING([--with-api-socket=PATH], [Path to socket (default none)]),
	api_socket=$withval,
	api_socket=
)

AC_ARG_WITH([api_db_user],
	AS_HELP_STRING([--with-api-db-user=USER], [DB username (default icinga)]),
	api_db_user=$withval,
	api_db_user=icinga
)

AC_ARG_WITH([api_db_passwd],
	AS_HELP_STRING([--with-api-db-passwd=PASSWD], [DB password (default icinga)]),
	api_db_passwd=$withval,
	api_db_passwd=icinga
)

AC_ARG_WITH([api_db_name],
	AS_HELP_STRING([--with-api-db-name=DBNAME], [DB name (default icinga)]),
	api_db_name=$withval,
	api_db_name=icinga
)

AC_ARG_WITH([api_db_prefix],
	AS_HELP_STRING([--with-api-db-prefix=PREFIX], [DB table prefiy (default icinga_)]),
	api_db_prefix=$withval,
	api_db_prefix=icinga_
)

# Custom vars
INSTALL_OPTS="-o $bin_user -g $bin_group"
CFLAGS=""

# Substitutions
AC_SUBST(web_user)
AC_SUBST(web_group)
AC_SUBST(web_path)
AC_SUBST(web_apache_path)
AC_SUBST(bin_user)
AC_SUBST(bin_group)

AC_SUBST(db_type)
AC_SUBST(db_host)
AC_SUBST(db_port)
AC_SUBST(db_name)
AC_SUBST(db_user)
AC_SUBST(db_pass)

AC_SUBST(api_type)
AC_SUBST(api_subtype)
AC_SUBST(api_host)
AC_SUBST(api_port)
AC_SUBST(api_socket)
AC_SUBST(api_db_user)
AC_SUBST(api_db_passwd)
AC_SUBST(api_db_name)
AC_SUBST(api_db_prefix)

AC_SUBST(icinga_api)

AC_SUBST(CFLAGS)
AC_SUBST(PHP)
AC_SUBST(MYSQL)
AC_SUBST(PHING)
AC_SUBST(INSTALL_OPTS)

AC_CONFIG_FILES([
	Makefile
	app/config/databases.xml
	app/config/icinga.xml
	app/modules/Web/config/module.xml
	etc/build.properties
	etc/tests/test.properties
	etc/apache2/icinga-web.conf
	pub/.htaccess
	pub/soap/.htaccess
])

AC_OUTPUT

ACICINGA_CLEANUP_APICONFIG([$api_type])
