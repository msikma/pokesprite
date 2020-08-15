#!/usr/bin/env python3

import sys
import json
from glob import glob
from os.path import relpath, abspath, dirname
from pathlib import Path

BASE_DIR = str(Path(dirname(abspath(__file__))).parent)

def get_json_files(base):
  '''Returns a list of all JSON files in the /data/ directory'''
  files = glob(f'{base}/data/**/*.json', recursive=True)
  files.sort()
  return files

def read_json_file(file):
  '''Reads a single JSON and returns a dict of its contents'''
  with open(file) as json_file:
    return json.load(json_file)

def print_json_error(file, err):
  '''Outputs error status'''
  print(f'{file}: {err}')

def main(base):
  '''Runs a check on all the project's JSON files to see if they have valid syntax'''
  files = get_json_files(base)
  errors = False

  for file in files:
    fpath = relpath(file, base)
    try:
      read_json_file(file)
    except json.decoder.JSONDecodeError as err:
      print_json_error(fpath, err)
      errors = True
    except:
      print_json_error(fpath, 'Unknown error')
      errors = True
  
  if errors:
    sys.exit(1)

if __name__== "__main__":
  main(BASE_DIR)
