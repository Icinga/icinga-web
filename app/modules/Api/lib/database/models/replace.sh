#!/bin/sh

for file in $( cd generated && ls *.php && cd ..);
do 
sed "s/'(.*) *AS *id/'id/gi" -r generated/$file > generated_ora/$file

done

