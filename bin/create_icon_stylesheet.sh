#!/bin/bash

DIR=$1
REL="../images/$(basename $DIR)"
EXT=".png"
PFX="icinga-cronk-icon-"

if [[ ! -d $DIR ]]; then
	echo "Directory is missing $0 <DIR>"
	exit 1
fi

echo "/** ---"
echo "DIR:    $DIR"
echo "REL:    $REL"
echo "EXT:    $EXT"
echo "Prefix: $PFX"
echo "--- **/"
echo ""
echo ""

for I in $DIR/*png; do
	FILE="$REL/$(basename "$I")"
	NAME=$(basename "$I" "$EXT")
	NAME=$(echo $NAME | sed -e 's/--/-/g' -e 's/ /-/g')
	NAME=$(echo $NAME | tr '[A-Z]' '[a-z]')
	NAME="$PFX$NAME"
	
	echo ".$NAME { background-image: url($FILE) !important; background-repeat: no-repeat;  }"

done


