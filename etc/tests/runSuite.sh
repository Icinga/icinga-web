#!/bin/sh
if type -P phpunit &> /dev/null == 0
then
	phpunit --syntax-check --verbose --configuration=config/suites.xml 
else
	echo "You need the newest phpunit package from pear in order to run the tests!"
fi
