dnl ------------------------
dnl icinga configure helpers
dnl ------------------------

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

   AS_IF([ test "XX${$1}" == "XXnot found" ],
	 [ AC_MSG_WARN([binary $2 not found in PATH]) ])

   test "XX${$1}" == "XXnot found" && $1=""
])

AC_DEFUN([ACICINGA_CHECK_API], [
	AC_MSG_CHECKING([for IcingaApi])
	AS_IF([ test -e $1/IcingaApi.php && $GREP -q "class IcingaApi" $1/IcingaApi.php ],
		[ AC_MSG_RESULT([found])    ],
		[ AC_MSG_ERROR([not found]) ])
])

AC_DEFUN([ACICINGA_EXTRACT_VERSION], [
	$1=`echo "$PACKAGE_VERSION" | $SED 's/^\([[0-9]]\+\)\.\([[0-9]]\+\)\.\([[0-9]]\+\)\(\-\(.\+\)\)\?$/\1/g'`
	$2=`echo "$PACKAGE_VERSION" | $SED 's/^\([[0-9]]\+\)\.\([[0-9]]\+\)\.\([[0-9]]\+\)\(\-\(.\+\)\)\?$/\2/g'`
	$3=`echo "$PACKAGE_VERSION" | $SED 's/^\([[0-9]]\+\)\.\([[0-9]]\+\)\.\([[0-9]]\+\)\(\-\(.\+\)\)\?$/\3/g'`
	$4=`echo "$PACKAGE_VERSION" | $SED 's/^\([[0-9]]\+\)\.\([[0-9]]\+\)\.\([[0-9]]\+\)\(\-\(.\+\)\)\?$/\5/g'`
])

AC_DEFUN([ACICINGA_REMOVE_BLOCK], [
	$SED -i -e "/###BEGIN_$2###/,/###END_$2###/d" $1
])

AC_DEFUN([ACICINGA_CLEANUP_APICONFIG], [
	FILE="$2"
	BLOCKS=`echo "CONNECTION_IDO CONNECTION_LIFESTATUS CONNECTION_FILE" | $SED "s/\s*$1//g"`
	AC_MSG_NOTICE([Create api config in $FILE])
	for T in $BLOCKS; do
		ACICINGA_REMOVE_BLOCK([$FILE],[$T])
	done
])

AC_DEFUN([ACICINGA_DEVEL_MODE], [
	AC_MSG_CHECKING([for devel-mode])
	AS_IF([ test "$devel_mode" == "yes" ], [
		flag_caching="false"
dnl		Setting this to true cause errors
dnl		for parrallel requests
		flag_debug="false"
		AC_MSG_RESULT([YES ... debug=false, jscache=false (!!)]) 
	], [ AC_MSG_RESULT([no (good)]) ])
])

AC_DEFUN([ACICINGA_PATH_GUESS], [
	$2=$3
	for x in $1; do
		AC_MSG_CHECKING([if path $x exists])
		AS_IF([test -d $x],
			  [AC_MSG_RESULT([found]); $2=$x; break],
			  [AC_MSG_RESULT([not found])]
		)
	done
])