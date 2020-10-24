#!/usr/bin/env bash

PROJECT="pokesprite/trim"
DESCRIPTION="Trims transparent border on PNG files and adds 3px transparent border."
SELF="trim.sh"
VERSION="1.0.0"

function check_prerequisites {
  arr=('mogrify')
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

function trim_multiple {
  for f in "$@"; do
    if [ ${f: -4} != ".png" ]; then
      continue
    fi
    if [ ! -f "$f" ]; then
      echo "$SELF: error: can't find file: $f"
      continue
    fi
    trim "$f"
  done
}

function trim_all {
  for f in "$@"; do
    if [[ -d "$f" ]]; then
      if [ ${f: -1} != "/" ]; then
        f+="/"
      fi
      for file in "$f"*; do
        trim_multiple "$file"
      done
    elif [[ -f $f ]]; then
      trim_multiple "$f"
    fi
    echo $f
  done
}

function argparse {
  if [[ ( -z "$1" ) || ( "$1" == "-h" ) ]]; then
    echo "usage: $SELF [-v] [-h] [files...]"
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
  trim_all "$@"
}

if [[ "${BASH_SOURCE[0]}" = "${0}" ]]; then
  argparse $@
fi
