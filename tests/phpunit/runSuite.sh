#!/bin/bash
PHPUNIT=$(which phpunit)
BASE=$(readlink -f $(dirname $0))
CACHE_DIR=$BASE/../../app/cache

function del_cache {
	if [ -d $CACHE_DIR/config ]; then
		echo -n "Deleting config cache ... "
		rm -rf $CACHE_DIR/config
		echo "OK"
	fi

	if [ -d $CACHE_DIR/content ]; then
		echo -n "Deleting content cache ... "
		rm -rf $CACHE_DIR/content
		echo "OK"
	fi
}

del_cache

echo "** Test begin **"
echo ""

if [ -x ${PHPUNIT} ]; then
	cd $BASE
	${PHPUNIT} --configuration=suites.xml $@
else
	echo "Sorry, PHPUnit not found."
fi

echo ""
echo ""
echo "** Test end **"

del_cache

