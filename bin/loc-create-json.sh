#!/bin/bash

INDIR=$1
OUTDIR=$2
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

for FILE in $INDIR/*.po; do
	NEWFILE=$(basename $FILE '.po').json
	echo "$PERL $BIN -p $FILE > $OUTDIR/$NEWFILE"
	$PERL $BIN -p $FILE > $OUTDIR/$NEWFILE
	echo "--> DONE"
done