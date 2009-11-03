#!/bin/bash
#
# Idea borrowed by pnp4nagios :-)  
#

DIR=$(dirname $0 )
IGNORE="example"
SRCDIR="app bin doc etc lib pub res"
cd $DIR/..

for DIR in $SRCDIR; do
	
	for TDIR in $(find $DIR -type d -printf "%P\n" | grep -v "$IGNORE"); do
		SOURCE="$DIR/$TDIR"
		
		if [[ "$SOURCE" != "" && -e "$SOURCE" ]]; then
			echo -e "\t\$(INSTALL) -m 755 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(prefix)/$SOURCE"
		fi
		
	done
	
	for FILE in $(find $DIR -type f -printf "%P\n" | grep -v "$IGNORE"); do
		SOURCE="$DIR/$FILE"
		
		if [[ "$SOURCE" != "" && -e "$SOURCE" ]]; then
			echo -e "\t\$(INSTALL) -m 644 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(prefix)/$SOURCE"
		fi
		
	done
	
done

exit 0

# [EOF]