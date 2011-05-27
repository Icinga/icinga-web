#!/bin/bash

DIR=$(dirname $(readlink -f $0))
STANDARD=$DIR/YACS
ROOT=$(readlink -f $DIR/../../)
TOTEST=app/
PHPCS=$(which phpcs)
OUTPUT=result.xml

if [ ! -x $PHPCS ]; then
	echo "phpcs binary not found!"
	exit 1
fi

$PHPCS \
	-v \
	--standard=$STANDARD \
	--report=checkstyle \
	--report-file=$DIR/$OUTPUT \
	--ignore="cache" \
	$ROOT/$TOTEST
