#!/bin/bash
php ./phing.php install-cronk
if [ $? == 0 ]; then
	php ./phing.php -f test.xml test
fi
