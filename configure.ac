#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

# Which version of AC
AC_PREREQ(2.61)

# Init project
AC_INIT(icinga-web, [1.0.1], [dev.icinga.org])

AC_PREFIX_DEFAULT(/usr/local/icinga-web)

# Checks for programs.
AC_PROG_INSTALL
AC_PROG_GREP
AC_PROG_SED

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
	AS_HELP_STRING([--with-web-user=<user>], [username for web writable files (default www-data)]),
	web_user=$withval,
	ACICINGA_USER_GUESS([www wwwrun www-data apache httpd nobody],[web_user],[www-data])
)

AC_ARG_WITH([web_group],
	AS_HELP_STRING([--with-web-group=<grp>], [group for web writable files (default www-data)]),
	web_group=$withval,
	ACICINGA_GROUP_GUESS([www www-data apache httpd nogroup nobody],[web_group], [www-data])
)

AC_ARG_WITH([web_path],
	AS_HELP_STRING([--with-web-path=<path>], [web sub path (default /icinga-web)]),
	web_path=$withval,
	web_path=/icinga-web
)

AC_ARG_WITH([bin_user],
	AS_HELP_STRING([--with-bin-user=<user>], [user for all other files (default root)]),
	bin_user=$withval,
	bin_user=root
)

AC_ARG_WITH([bin_group],
	AS_HELP_STRING([--with-bin-group=<grp>], [group for all other files (default bin)]),
	bin_group=$withval,
	bin_group=bin
)

# Api related
AC_ARG_WITH([icinga_api],
	AS_HELP_STRING([--with-icinga-api=<string>], [Path to icinga-api]),
	icinga_api=$withval,
	icinga_api=/usr/local/icinga-api
)

ACICINGA_CHECK_API([$icinga_api])

# Web database switches
AC_ARG_WITH([db_type],
	AS_HELP_STRING([--with-db-type=<string>], [Type of dbms (default mysql)]),
	db_type=$withval,
	db_type=mysql
)

AC_ARG_WITH([db_host],
	AS_HELP_STRING([--with-db-host=<string>], [Host of dbms (default 127.0.0.1)]),
	db_host=$withval,
	db_host=127.0.0.1
)

AC_ARG_WITH([db_port],
	AS_HELP_STRING([--with-db-port=<int>], [Port of the dbms (default 3306)]),
	db_port=$withval,
	db_port=3306
)

AC_ARG_WITH([db_name],
	AS_HELP_STRING([--with-db-name=<string>], [DB name for icinga (default icinga_web)]),
	db_name=$withval,
	db_name=icinga_web
)

AC_ARG_WITH([db_user],
	AS_HELP_STRING([--with-db-user=<string>], [DB user for icinga (default icinga_web)]),
	db_user=$withval,
	db_user=icinga_web
)

AC_ARG_WITH([db_pass],
	AS_HELP_STRING([--with-db-pass=<string>], [DB pass for icinga (default icinga_web)]),
	db_pass=$withval,
	db_pass=icinga_web
)

# Custom vars
INSTALL_OPTS="-o $bin_user -g $bin_group"
CFLAGS=""

# Substitutions
AC_SUBST(web_user)
AC_SUBST(web_group)
AC_SUBST(web_path)
AC_SUBST(bin_user)
AC_SUBST(bin_group)

AC_SUBST(db_type)
AC_SUBST(db_host)
AC_SUBST(db_port)
AC_SUBST(db_name)
AC_SUBST(db_user)
AC_SUBST(db_pass)

AC_SUBST(icinga_api)

AC_SUBST(CFLAGS)
AC_SUBST(PHP)
AC_SUBST(MYSQL)
AC_SUBST(PHING)
AC_SUBST(INSTALL_OPTS)

AC_CONFIG_FILES([
	Makefile
	etc/build.properties
	etc/tests/test.properties
	app/config/databases.xml
	pub/.htaccess
	pub/soap/.htaccess
])

AC_OUTPUT
