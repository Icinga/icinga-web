#!/bin/bash
 
  echo
  echo Rabbit Droppings 1.0
  echo By Robbie Ferguson, www.category5.tv
 
if [ -z "$1" ]; then
  echo
  echo This script clears out Windows, Mac and Linux temp files
  echo \(I call them rabbit droppings\) from the current
  echo or specified folder.
  echo
  echo Because you have not specified a folder, I\'m clearing the
  echo current folder, recursively of any rabbit droppings.
  echo
  echo Usage: $0 folder
  echo Where folder is the folder or mountpoint you\'d like to clean up.
  echo
  echo Example: $0 \/home \<- will clear out the temp files
  echo          from your \/home folder, recursively
fi
  echo
 
if [ $1 ]; then
  echo Detecting rabbit droppings recursive to $1...
else
  echo Detecting rabbit droppings recursive to your current folder...
fi
  echo
 
#output the list
find 2>/dev/null $1 -iname "*~" -o -iname "._*" -o -iname ".DS_Store" -o -iname "Thumbs.db" 
 
read -p "Okay to delete the above rabbit droppings? (Y/N) "
if [ "$REPLY" = "y" ] ; then 
  APPROVE="1"
fi
if [ "$REPLY" = "Y" ] ; then 
  APPROVE="1"
fi
 
  echo
 
if [ "$APPROVE" = "1" ]; then
  #remove the files
  find 2>/dev/null $1 -iname "._*" -exec rm -rf {} \;
  find 2>/dev/null $1 -iname "*~" -exec rm -rf {} \;
  find 2>/dev/null $1 -iname ".DS_Store" -exec rm -rf {} \;
  find 2>/dev/null $1 -iname "Thumbs.db" -exec rm -rf {} \;
else
  echo Cancelled.
fi