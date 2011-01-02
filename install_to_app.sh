#!/usr/bin/env bash

if [ -z "$1" -o ! -e "$1" ]; then echo "usage: $0 <app_root_dir>"; exit; fi

#
# symlink dirs content where they belong in a cake app
#
src=( behaviors components  helpers layouts vendors views js)
dst=( models    controllers views   views   '.'      '.'  webroot) 
count=${#src[@]}
i=0
script_dir=$(./canon_path.sh .) 

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
		echo "ln -s $s_a $d_a/$f"
		dm="${dst[$i]}/$s/$f"
		dm=${dm#./}
		echo "$dm" >> "$1/.gitignore"
	done

	let i=i+1
done

 
# if there already is a symlink remove it
if [ -h "$1/fixups" ]; then
	rm "$1/fixups"
fi

# symlink the fixups script 
if [ ! -e "$1/fixups" ]; then
	ln -s "$script_dir/fixups" "$1/fixups"
	echo "fixups" >> "$1/.gitignore"
fi

