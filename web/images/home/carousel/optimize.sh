#!/bin/bash

for file in $(ls original)
do
  convert \
    "original/$file" \
    -strip \
    -define jpeg:extent=100KB \
    ${file%.*}.jpg
done
