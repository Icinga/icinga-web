#!/bin/sh
PHPUNIT=$(which phpunit)
if [ -x ${PHPUNIT} ]
then
	${PHPUNIT} --verbose --configuration=config/suites.xml 
else
	echo "Sorry, PHPUnit not found."
fi
