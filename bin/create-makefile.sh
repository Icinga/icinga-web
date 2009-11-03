#!/bin/sh
#
# PNP4Nagios Helper Script
#
DIR=`dirname $0`
DIR=./
#cd $DIR/../lib/kohana
cd $DIR/framework

for D in `find . -type d -printf "%P\n" | grep -v "examples"`;do
        if [ "$D" != "" ];then
                echo -e "\t\$(INSTALL) -m 755 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(prefix)/$D"
        fi
done
for F in `find . -type f -printf "%P\n" | grep -v "examples"`;do
        if [ "$F" != "" ];then
                echo -e "\t\$(INSTALL) -m 644 \$(INSTALL_OPTS) framework/$F \$(DESTDIR)\$(prefix)/$F"
        fi
done
