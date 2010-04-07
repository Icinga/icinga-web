#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

# Which version of AC
AC_PREREQ(2.61)

# Init project
AC_INIT(icinga-web, [0.9.1-alpha], [dev.icinga.org])

AC_PREFIX_DEFAULT(/usr/local/icinga-web)

# Checks for programs.
AC_PROG_INSTALL
AC_PROG_GREP
AC_PROG_SED

# Check for php
AC_ARG_VAR([PHP],[Perl interpreter command])
AC_PATH_PROG([PHP], [php], [not found])

if test "$PHP" = 'not found'; then
        AC_MSG_ERROR([cannot use $PACKAGE_NAME without php])
fi

# Check for mysql
AC_ARG_VAR([MYSQL],[MySql client binary])
AC_PATH_PROG([MYSQL], [mysql], [not found])

if test "$MYSQL" = 'not found'; then
        AC_MSG_ERROR([cannot use $PACKAGE_NAME without MySql client binary])
fi

AC_ARG_VAR([PRINTF],[Printf coreutil])
AC_PATH_PROG([PRINTF], [printf], [not found])

if test "$PRINTF" = 'not found'; then
        AC_MSG_ERROR([cannot use $PACKAGE_NAME without printf core util])
fi

# Custom macros

# ACRT_USER_EXISTS( users, variable, default )
# - users is a list of users [www apache www-docs]
#    from highest to lowest priority to high priority (i.e. first match)
# - variable is what you set with the result
#

AC_DEFUN([ACICINGA_USER_GUESS],
 [
   $2=$3
   for x in $1; do
    AC_MSG_CHECKING([if user $x exists])
     AS_IF([ $GREP -q "^$x:" /etc/passwd ],
           [ AC_MSG_RESULT([found]); $2=$x ; break],
           [ AC_MSG_RESULT([not found]) ])
   done
  ])
AC_DEFUN([ACICINGA_GROUP_GUESS],
 [
   $2=$3
   for x in $1; do
    AC_MSG_CHECKING([if group $x exists])
     AS_IF([ $GREP -q "^$x:" /etc/group ],
           [ AC_MSG_RESULT([found]); $2=$x ; break],
           [ AC_MSG_RESULT([not found]) ])
   done
  ])



# Checks for libraries.

# Checks for header files.

# Checks for typedefs, structures, and compiler characteristics.

# Checks for library functions.

# Define install options

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

AC_ARG_WITH([icinga_api],
	AS_HELP_STRING([--with-icinga-api=<string>], [Path to icinga-api]),
	icinga_api=$withval,
	icinga_api=/usr/local/icinga-api
)

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
AC_SUBST(PRINTF)
AC_SUBST(INSTALL_OPTS)

AC_CONFIG_FILES([
	Makefile
	etc/build.properties
	app/config/databases.xml
])

AC_OUTPUT
