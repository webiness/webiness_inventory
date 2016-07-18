#!/bin/sh

for f in `find -name '*.php'`
do
    echo "{" > lang/template.txt
    cat $f | grep 'WsLocalize::msg' | sed 's/.*WsLocalize::msg(//' | sed 's/).*//' >> ws_list.txt
    while read l
    do
	# skip empty lines
	if [ "$l" = "" ]
	then
	    continue
	fi
	
	echo "    $l:$l," >> lang/template.txt
    done < ws_list.txt
    
    echo "}" >> lang/template.txt
done
    cat lang/template.txt | sed "s/'/\"/g" > lang/temp.txt 
    rm lang/template.txt
    rm ws_list.txt