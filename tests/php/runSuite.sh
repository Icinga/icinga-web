#!/bin/sh
PHPUNIT=$(which phpunit)
if [ -x ${PHPUNIT} ]
then
	cd $(dirname $0)
	rm -r ../../app/cache/content ../../app/cache/config 2>/dev/null
	${PHPUNIT} --verbose --configuration=suites.xml 
else
	echo "Sorry, PHPUnit not found."
fi
