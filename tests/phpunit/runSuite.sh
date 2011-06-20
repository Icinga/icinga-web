#!/bin/sh
rm /usr/local/icinga-web/app/cache/*/*
PHPUNIT=$(which phpunit)
if [ -x ${PHPUNIT} ]
then
	${PHPUNIT} --configuration=suites.xml 
else
	echo "Sorry, PHPUnit not found."
fi

