#!/bin/bash

INDIR=$1
OUTDIR=$2
TEXTDOMAIN=default
BIN=$(dirname $0)/../lib/jsgettext/bin/po2json
PERL=$(which perl)

if [ ! -x "$BIN" ]; then
	echo "$BIN is not executable!"
	exit 1
fi

if [ ! -d "$INDIR" ]; then
	echo "ARG1 is source dir, should exist"
	exit 1
fi

if [ ! -d "$OUTDIR" ]; then
	echo "ARG2 is target dir, should exist"
	exit 1
fi;

for FILE in $INDIR/*; do
	LOC="$(basename $FILE)"
	POF="$INDIR/$LOC/$TEXTDOMAIN.po"
	if [ -e "$POF" ]; then
		NEWFILE="$OUTDIR/$LOC.json"
		TF="$INDIR/$LOC/$LOC.po"

		cp $POF $TF
		echo -n "create $NEWFILE from $TF"
		$PERL $BIN $TF > $NEWFILE
		rm -f $TF
	fi
done
