#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

# Which version of AC
AC_PREREQ(2.61)

# Init project
AC_INIT(icinga-web, [0.9.1], [dev.icinga.org])

AC_PREFIX_DEFAULT(/usr/local/icinga-web)

# Checks for programs.
AC_PROG_INSTALL
AC_PROG_GREP

# Check for php
AC_ARG_VAR([PHP],[Perl interpreter command])
AC_PATH_PROG([PHP], [php], [not found])

if test "$PHP" = 'not found'; then
        AC_MSG_ERROR([cannot use $PACKAGE_NAME without php])
fi

# Custom macros

# ACRT_USER_EXISTS( users, variable, default )
# - users is a list of users [www apache www-docs]
#    from highest to lowest priority to high priority (i.e. first match)
# - variable is what you set with the result
#

AC_DEFUN([ACRT_USER_GUESS],
 [
   $2=$3
   for x in $1; do
    AC_MSG_CHECKING([if user $x exists])
     AS_IF([ $GREP -q "^$x:" /etc/passwd ],
           [ AC_MSG_RESULT([found]); $2=$x ; break],
           [ AC_MSG_RESULT([not found]) ])
   done
  ])
AC_DEFUN([ACRT_GROUP_GUESS],
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
AC_ARG_WITH(web_user,--with-web-user=<user> sets user name to run icinga,web_user=$withval,ACRT_USER_GUESS([www www-data apache httpd nobody],[web_user],[www-data]))
AC_ARG_WITH(web_group,--with-web-group=<group> sets group name to run icinga,web_group=$withval,ACRT_GROUP_GUESS([www www-data apache httpd nogroup nobody],[web_group], [www-data]))
AC_SUBST(web_user)
AC_SUBST(web_group)
AC_DEFINE_UNQUOTED(DEFAULT_web_user,"$web_user")
AC_DEFINE_UNQUOTED(DEFAULT_web_group,"$web_group")

AC_ARG_WITH(bin_user,--with-bin-user=<user> sets user name for common webfiles,bin_user=$withval,bin_user=root)
AC_ARG_WITH(bin_group,--with-bin-group=<group> sets group for common webfiles,bin_group=$withval,bin_group=bin)
AC_SUBST(bin_user)
AC_SUBST(bin_group)
AC_DEFINE_UNQUOTED(DEFAULT_web_user,"$bin_user")
AC_DEFINE_UNQUOTED(DEFAULT_web_group,"$bin_group")

BIN_USER="$bin_user"
AC_SUBST(bin_user)

BIN_GROUP="$bin_group"
AC_SUBST(bin_group)

INSTALL_OPTS="-o $bin_user -g $bin_group"
AC_SUBST(INSTALL_OPTS)

CFLAGS=""
AC_SUBST(CFLAGS)

AC_CONFIG_FILES([Makefile])

AC_OUTPUT
