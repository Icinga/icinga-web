#!/bin/bash
TEMPLATE="$1"
TARGETS="$2"
TEMPLATE_DIR="templates"
for I in $TARGETS/*; do
	if [[ ! $I =~ $TEMPLATE_DIR  ]]; then
		LOC="$(basename $I)"
		POF=$I/$(basename $TEMPLATE .pot).po
		if [[ -e "$POF" ]]; then
			echo -n "MERGE $POF ($LOC) "
			msgmerge -v -U "$POF" "$TEMPLATE"
		else
			echo -n "INIT $POF ($LOC) "
			msginit -i "$TEMPLATE" -o "$POF" -l "LOC" --no-translator --no-wrap
		fi
	fi
done

exit 0
