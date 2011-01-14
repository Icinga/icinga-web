#!/bin/sh
for file in $(ls *.php);
do 
sed 's/Doctrine_Table/Icinga_Doctrine_Table/g' $file > dummy
mv dummy $file;
done

