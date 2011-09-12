#!/bin/bash

DIR=$(dirname $0)
ROOT=$DIR/../etc/database/rescue/mysql
DUMP=$(which mysqldump)


if [[ ! -d $ROOT ]]; then
	echo "$ROOT does not exist"
	exit 1
fi

echo "Using directory: $ROOT"
echo ""
cd $ROOT

FILES=$(ls *sql 2>/dev/null)

if [[ -n $FILES ]]; then

	echo -n "Deleting all existing files: "
	echo $FILES
	read -i "n" -p "Are you sure? ([n]/YES) "

	if [[ "$REPLY" == "YES" ]]; then
		echo -n "Deleting all files ... "
		echo $FILES | xargs rm -f
		echo " OK" 
	fi

fi

read  -p "DB name (icinga_web): " "DB_NAME"
read  -p "DB user (icinga_web): " "DB_USER"
read  -s -p "DB pass (icinga_web): " "DB_PASS"

[[ -z $DB_NAME ]] && DB_NAME="icinga_web"
[[ -z $DB_USER ]] && DB_USER="icinga_web"
[[ -z $DB_PASS ]] && DB_PASS="icinga_web"

echo ""

DUMP_ARGS="--compatible=mysql40 --user=$DB_USER --password=$DB_PASS $DB_NAME"
TSTAMP=$(date +%Y%m%d%H%M%S)

echo -n "Create schema ... "
$DUMP --no-data $DUMP_ARGS > $TSTAMP-rescue-schema.sql
echo "OK"

echo -n "Create data dump ... "
$DUMP --no-create-db --no-create-info $DUMP_ARGS > $TSTAMP-rescue-data.sql
echo "OK"

exit 0




