#!/usr/bin/env php
<?php

# pngcrush_all.php: runs all images in a directory through pngcrush
# and saves the new file over the old file. Use with care.
#
# This script requires a pngcrush binary.

$verbose = false;
$dir = @rtrim(trim($argv[1]), '/');
$GLOBALS['file_exts'] = array('png');
$pngcrush_path = '../tools/pngcrush';
$pngcrush_cmd = $pngcrush_path.' -ow -fix -reduce -force -nofilecheck -brute -rem alla -oldtimestamp "%s"';

if (!isset($dir)) {
  print('usage: pngcrush_all.php dir'.PHP_EOL.'pngcrush_all.php: error: too few arguments'.PHP_EOL);
  exit();
}

if (!is_dir($dir)) {
  print('usage: pngcrush_all.php dir'.PHP_EOL.'pngcrush_all.php: error: not a directory or not directory not accessible'.PHP_EOL);
  exit();
}

if (!is_file($pngcrush_path)) {
  print('usage: pngcrush_all.php dir'.PHP_EOL.'pngcrush_all.php: error: could not find pngcrush (tried: `'.$pngcrush_path.'\')'.PHP_EOL);
  exit();
}

$imgs = iterate_dir($dir);

$counter = 0;
$total = count($imgs);

print('dir: `'.$dir.'\' contains '.$total.' image files.'.PHP_EOL);

foreach ($imgs as $path => $img) {
  $old_size = filesize($path);
  $cmd = sprintf($pngcrush_cmd.' 2>&1', $path);
  exec($cmd, $output, $code);
  if ($code !== 0) {
    print(PHP_EOL.'pngcrush_all.php: error: there was a problem running `pngcrush\':'.PHP_EOL.PHP_EOL.'    '.$cmd.PHP_EOL);
    die();
  }
  clearstatcache();
  $new_size = filesize($path);
  print('Modified `'.$path.'\'. Old size: '.$old_size.'. New size: '.$new_size.'.'.PHP_EOL);
}

function iterate_dir($dir)
{
  $stack = array();
  try {
    $dir_it = new \DirectoryIterator($dir);
  } catch (Exception $e) {
    print('error: can\'t open directory: '.$dir);
    continue;
  }
  foreach ($dir_it as $file) {
    // Some checks to ensure it's a valid image.
    if ($file->isDot()) {
      continue;
    }
    if ($file->isDir()) {
      $dir_stack = iterate_dir($dir.'/'.$file->getFilename());
      $stack = array_merge($dir_stack, $stack);
      continue;
    }
    $fn = $file->getFilename();
    $fn_bits = explode('.', $fn);
    $fn_ext = strtolower(trim(end($fn_bits)));
    $file_path = $dir.'/'.$fn;
    if (!in_array($fn_ext, $GLOBALS['file_exts'])) {
      continue;
    }
    $stack[$file_path] = true;
  }
  return $stack;
}