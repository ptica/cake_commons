#!/usr/bin/env bash

system=`uname`;

if [ $system = 'FreeBSD' ]
then
	echo $(realpath $1)
elif [ $system = 'Darwin' ]
then
	#alias realpath="python -c 'import os, sys; print os.path.realpath(sys.argv[1])'"
	echo $(python -c 'import os, sys; print os.path.realpath(sys.argv[1])' $1)
else
	echo $(readlink -f $1)
fi

