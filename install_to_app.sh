#!/usr/bin/env bash

if [ -z "$1" -o ! -e "$1" ]; then echo "usage: $0 <app_dir>"; exit; fi

src=( behaviors components  helpers layouts vendors views)
dst=( models    controllers views   views   '.'      '.') 
count=${#src[@]}
i=0

while [ "$i" -lt "$count" ]; do
	s=${src[$i]}
	d="$1/${dst[$i]}"

	# 
	for f in $(ls $s); do
		s_a=$(./canon_path.sh $s/$f)
		d_a=$(./canon_path.sh $d/$s)
		
		`ln -s $s_a $d_a/$f`
	done

	let i=i+1
done

