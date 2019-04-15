#!/usr/bin/env python3

# prints JSON-usable japanese
# example: $ ./jsonjp.py モクロー
# output: "\u30e2\u30af\u30ed\u30fc"
import json
import sys
print(json.dumps(sys.argv[1]))