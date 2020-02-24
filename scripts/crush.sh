#!/usr/bin/env bash

PROJECT="pokesprite/crush"
DESCRIPTION="Minimizes PNG files by running pngcrush."
SELF="crush.sh"
VERSION="1.0.0"

function check_prerequisites {
  arr=('pngcrush')
  for tool in "${arr[@]}"; do
    if ! command -v $tool >/dev/null 2>&1; then
      echo "$SELF: error: the '$tool' command is not available"
      exit
    fi
  done
}

function crush {
  pngcrush -ow -fix -force -nofilecheck -brute -rem alla -oldtimestamp "$1"
}

function crush_all {
  for f in "$@"; do
    if [ ${f: -4} != ".png" ]; then
      continue
    fi
    if [ ! -f "$f" ]; then
      echo "$SELF: error: can't find file: $f"
      continue
    fi
    crush "$f"
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
  crush_all "$@"
}

if [[ "${BASH_SOURCE[0]}" = "${0}" ]]; then
  argparse $@
fi
