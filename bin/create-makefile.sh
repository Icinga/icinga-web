#!/bin/bash
#
# Idea borrowed by pnp4nagios :-)  
#

DIR="$1"
IGNORE=".*/(.*xml_.*\.php|\.(git|#)|.*\.in$|data/i18n/po|etc\/sitecfg|cache/|app\/data|agavi/samples).*"
SRCDIR="app bin doc etc lib pub etc"

declare -i DCOUNT=0
declare -i FCOUNT=0

cd $DIR

echo "# INSTALL_FILES_BEGIN"

function pline {
	if [[ -d $1 ]]; then
		DCOUNT+=1
		echo -e "\t\$(INSTALL) -m 755 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(prefix)/$1"
	elif [[ -a $1 ]]; then
		FCOUNT+=1
		echo -e "\t\$(INSTALL) -m 644 \$(INSTALL_OPTS) $1 \$(DESTDIR)\$(prefix)/$1"
	fi
}

for X in $(find $SRCDIR -noleaf  -regextype posix-awk -not -regex $IGNORE | sort -d); do
		pline $X
done

echo "INC_FILES=$FCOUNT"
echo "INC_DIRS=$DCOUNT"

echo "# INSTALL_FILES_END"

exit 0

# [EOF]
