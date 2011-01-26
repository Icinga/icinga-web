#!/bin/sh
for file in $(ls *.php);
do 
sed 's/IcingaInstances ^s]/IcingaInstances /g' $file > dummy
mv dummy $file;
done

