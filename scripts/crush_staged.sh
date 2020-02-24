#!/usr/bin/env bash

PROJECT="pokesprite/crush_staged"
DESCRIPTION="Minimizes all PNG files currently staged for commit."
SELF="crush_staged.sh"
VERSION="1.0.0"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null 2>&1 && pwd)"

function argparse {
  if [ "$1" == "-h" ]; then
    echo "usage: $SELF [-v] [-h]"
    echo "$DESCRIPTION"
    exit
  fi
  if [ "$1" == "-v" ]; then
    echo "$PROJECT-$VERSION"
    exit
  fi

  files=$(git diff --name-only --staged)
  if [ -z "$files" ]; then
    echo "$SELF: error: no files have been staged"
    exit 1
  fi
  exec "$BASE"/crush.sh $files
}

if [[ "${BASH_SOURCE[0]}" = "${0}" ]]; then
  argparse $@
fi
