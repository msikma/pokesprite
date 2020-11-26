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
  # Note: adds a 1px transparent border before trimming to ensure that only transparent pixels are removed.
  # Add e.g. "-border 2x2" right before the output (before PNG32:..) to add transparent padding.
  magick convert "$1" -bordercolor none -border 1x1 -trim +repage PNG32:"$1"
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
