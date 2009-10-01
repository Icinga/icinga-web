#/bin/bash

#
# updatepo.sh - generates all language files
#
# created 2008 by Marius Hein <marius.hein@netways.de>
# modified for Icinga-Web 2009 by Christian Doebler <christian.doebler@netways.de>
#

TEXTDOMAIN=icinga
INDIR=po
OUTDIR=mo
BIN=$(which msgfmt)

for IT in $INDIR/*.po; do
	NEWFILE=$(basename $IT '.po').mo
	echo -n "$IT -> $OUTDIR/$NEWFILE: "
	$BIN $IT -v -o $OUTDIR/$NEWFILE
done
