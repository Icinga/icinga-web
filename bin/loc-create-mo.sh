#!/bin/bash

#
# updatepo.sh - generates all language files
#
# created 2008 by Marius Hein <marius.hein@netways.de>
# modified for Icinga-Web 2009 by Christian Doebler <christian.doebler@netways.de>
#

TEXTDOMAIN=default
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

for IT in $INDIR/*; do
	LOC="$(basename $IT)"
	POF="$IT/$TEXTDOMAIN.po"
	if [ -e "$POF" ]; then
		NEWFILE="$OUTDIR/$LOC.mo"
		echo -n "Creating $NEWFILE from $POF: "
		$BIN $POF -v -o $NEWFILE
	fi
done

exit 0


