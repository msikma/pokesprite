#!/usr/bin/env bash

PROJECT="pokesprite/outline"
DESCRIPTION="Adds a Gen 8 style white outline to an icon (requires ImageMagick)."
SELF="outline.sh"
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

function add_outline {
  magick convert "$1" -background white -alpha background -channel a -morphology dilate square:1 +channel PNG32:"$1"
}

function add_outline_all {
  for f in "$@"; do
    if [ ${f: -4} != ".png" ]; then
      continue
    fi
    if [ ! -f "$f" ]; then
      echo "$SELF: error: can't find file: $f"
      continue
    fi
    add_outline "$f"
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
  add_outline_all "$@"
}

if [[ "${BASH_SOURCE[0]}" = "${0}" ]]; then
  argparse $@
fi
