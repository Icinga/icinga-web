#!/bin/sh
PHPUNIT=$(which phpunit)
BASE=$(readlink -f $(dirname $0))
if [ -x ${PHPUNIT} ]
then
	cd $BASE
	${PHPUNIT} --configuration=suites.xml $@
else
	echo "Sorry, PHPUnit not found."
fi

