#! /bin/sh
phpunit --bootstrap /usr/local/icinga-web/etc/tests/icingaWebTesting.php /usr/local/icinga-web/etc/tests/tests/bootstrap/agaviBootstrapTest.php
exit $?
