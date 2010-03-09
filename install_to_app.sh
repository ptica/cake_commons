#!/usr/bin/env bash

if [ -z "$1" -o ! -e "$1" ]; then echo "usage: $0 <app_root_dir>"; exit; fi

src=( behaviors components  helpers layouts vendors views)
dst=( models    controllers views   views   '.'      '.') 
count=${#src[@]}
i=0

echo "# -- added by cake_common install script" >> "$1/.gitignore"

while [ "$i" -lt "$count" ]; do
	s=${src[$i]}
	d="$1/${dst[$i]}"

	# 
	for f in $(ls $s); do
		s_a=$(./canon_path.sh $s/$f)
		d_a=$(./canon_path.sh $d/$s)
		
		if [ -e "$d_a/$f" ]; then `rm "$d_a/$f"`; fi
		
		`ln -s $s_a $d_a/$f`
		dm="${dst[$i]}/$s/$f"
		dm=${dm#./}
		echo "$dm" >> "$1/.gitignore"
	done

	let i=i+1
done

