
AC_DEFUN([ACICINGA_USER_GUESS],[
   $2=$3
   for x in $1; do
    AC_MSG_CHECKING([if user $x exists])
     AS_IF([ $GREP -q "^$x:" /etc/passwd ],
           [ AC_MSG_RESULT([found]); $2=$x ; break],
           [ AC_MSG_RESULT([not found]) ])
   done
  ])

AC_DEFUN([ACICINGA_GROUP_GUESS],[
   $2=$3
   for x in $1; do
    AC_MSG_CHECKING([if group $x exists])
     AS_IF([ $GREP -q "^$x:" /etc/group ],
           [ AC_MSG_RESULT([found]); $2=$x ; break],
           [ AC_MSG_RESULT([not found]) ])
   done
])

AC_DEFUN([ACICINGA_CHECK_BIN], [
   AC_PATH_PROG([$1],[$2],[not found])

   AS_IF([ test "$2"  == "not found" ],
	 [ AC_MSG_ERROR([binary $3 not found in PATH]) ])

   AS_IF([ test -x "$2" ],
	 [ AC_MSG_ERROR([$3 not executable]) ])
])

