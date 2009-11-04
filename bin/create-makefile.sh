#!/bin/bash
#
# Idea borrowed by pnp4nagios :-)  
#

DIR=$(dirname $0 )
IGNORE="~|development__"
SRCDIR="app bin doc etc lib pub res"
cd $DIR/..

for DIR in $SRCDIR; do
	
	if [[ "$DIR" != "" && -e "$DIR" ]]; then
		echo -e "\t\$(INSTALL) -m 755 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(prefix)/$DIR"
	fi
	
	for TDIR in $(find $DIR -type d -printf "%P\n" | egrep -v "$IGNORE"); do
		SOURCE="$DIR/$TDIR"
		
		if [[ "$SOURCE" != "" && -e "$SOURCE" ]]; then
			echo -e "\t\$(INSTALL) -m 755 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(prefix)/$SOURCE"
		fi
		
	done
	
done

for DIR in $SRCDIR; do
	
	for FILE in $(find $DIR -type f -printf "%P\n" | egrep -v "$IGNORE"); do
		SOURCE="$DIR/$FILE"
		
		if [[ "$SOURCE" != "" && -e "$SOURCE" ]]; then
			echo -e "\t\$(INSTALL) -m 644 \$(INSTALL_OPTS) $SOURCE \$(DESTDIR)\$(prefix)/$SOURCE"
		fi
		
	done
	
done

exit 0

# [EOF]