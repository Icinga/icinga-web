#/bin/bash

#
# updatepo.sh - generates all language files
#
# created 2008 by Marius Hein <marius.hein@netways.de>
# modified for Icinga-Web 2009 by Christian Doebler <christian.doebler@netways.de>
#

TEXTDOMAIN=icinga
INDIR=$1
OUTDIR=$2
BIN=$(which msgfmt)

if [ ! -d "$INDIR" ]; then
	echo "ARG1 is sourcedir, should exist!"
	exit 1
fi

if [ ! -d "$OUTDIR" ]; then
	echo "ARG2 is targetdir, should exist!"
	exit 1
fi

for IT in $INDIR/*.po; do
	NEWFILE=$(basename $IT '.po').mo
	echo -n "$IT -> $OUTDIR/$NEWFILE: "
	$BIN $IT -v -o $OUTDIR/$NEWFILE
done

exit 0