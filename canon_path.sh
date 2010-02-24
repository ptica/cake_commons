#!/usr/bin/env bash

system=`uname`

if [ $system='FreeBSD' ];
then
	echo $(realpath $1)
else
	echo $(readlink -f $1)
fi

