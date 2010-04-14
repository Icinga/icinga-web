#!/bin/bash

PATTERN="-iname .DS_Store -o -iname Thumbs.db -o -iname *~ -o -iname ._* -o -iname *.bak"
ACTION="-print"
DIR="$1"

for F in `find $DIR $PATTERN`; do
	REPLY=
	
	if [[ ! -n $RMTMP_QUITE ]]; then
		echo -n "Delete $F"
	fi
	
	if [[ -n $RMTMP_RM ]]; then
		if [[ ! -n $RMTMP_FORCE ]]; then
			read -p " Are you sure (y/n)?"
		fi
	fi
	
	if [[ -n $RMTMP_FORCE || $REPLY == "y" ]]; then
		rm -rf $F
	fi
done

exit 0