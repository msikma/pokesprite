#!/usr/bin/env python3

# Prints input with non-ASCII characters converted to Unicode escape sequences.
# Example:
#   $ ./jsonjp.py モクロー
#   "\u30e2\u30af\u30ed\u30fc"

import json
import sys
if len(sys.argv) <= 1:
  print('usage: jsonjp.py TEXT')
  sys.exit(1)
print(json.dumps(sys.argv[1]))
