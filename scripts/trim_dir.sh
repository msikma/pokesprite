#!/usr/bin/env bash

PROJECT="pokesprite/trim_dir"
DESCRIPTION="Trims off transparent pixels from all images in a directory."
SELF="trim_dir.sh"
VERSION="1.0.0"

function check_prerequisites {
  arr=('magick')
  for tool in "${arr[@]}"; do
    if ! command -v $tool >/dev/null 2>&1; then
      echo "$SELF: error: the '$tool' command is not available"
      exit
    fi
  done
}

function trim {
  # trim transparent pixels
  echo "trimming $1"
  convert "$1" -trim +repage "$1"
#   magick mogrify -path "$1" -trim +repage -format png *.png
  # add 3px border
  convert "$1" -bordercolor transparent -border 3 "$1"
#   mogrify -path fullpathto/temp2 -resize 60x60% -quality 60 -format jpg *.png
#   magick mogrify -path $1 -bordercolor transparent -border 3 -format png *.png
}

function trim_dir {
  for d in "$@"; do
    find "$d" -name '*.png' -print0 |
    while IFS= read -r -d '' f; do
      trim "$f"
    done
  done
}

function argparse {
  if [[ ( -z "$1" ) || ( "$1" == "-h" ) ]]; then
    echo "usage: $SELF [-v] [-h] path[, path[, ...]]"
    if [ "$1" == "-h" ]; then
      echo "$DESCRIPTION"
      exit 0
    fi
    exit 1
  fi
  if [ "$1" == "-v" ]; then
    echo "$PROJECT-$VERSION"
    exit
  fi

  check_prerequisites
  trim_dir "$@"
}

if [[ "${BASH_SOURCE[0]}" = "${0}" ]]; then
  argparse $@
fi
