#!/usr/bin/env php
<?php

# change_outline.php: modifies the outline color of a directory of sprites.
#
# Use with care, overwrites files.

$dir = @rtrim(trim($argv[1]), '/');
$GLOBALS['file_exts'] = array('png');

if (!isset($dir)) {
  print('usage: change_outline.php dir'.PHP_EOL.'change_outline.php: error: too few arguments'.PHP_EOL);
  exit();
}

$imgs = iterate_dir($dir);
$counter = 0;
$total = count($imgs);

print('dir: `'.$dir.'\' contains '.$total.' image files.'.PHP_EOL);

foreach ($imgs as $path => $img) {
  $i = imagecreatefrompng($path);

  // Turn on for debugging:
  //$path_bits = explode('.', $path);
  //$new_name = implode('.', array_slice($path_bits, 0, -1)).'2.png';
  $new_name = $path;

  imagealphablending($i, false);
  imagesavealpha($i, true);
  $changed = change_color($i);
  imagepng($i, $new_name, 9);
  print('img: '.$path.' (changed: '.($changed === true ? 'T' : 'F').')'.PHP_EOL);
}

function change_color($i)
{
  $changed = false;
  for ($x = imagesx($i); $x--;) {
    for ($y = imagesy($i); $y--;) {
      $rgb = imagecolorat($i, $x, $y);
      $c = imagecolorsforindex($i, $rgb);
      if (hex_color($c) === '31313100') {
        $new_c = imagecolorallocatealpha($i, 32, 32, 32, $c['alpha']);
        imagesetpixel($i, $x, $y, $new_c);
        $changed = true;
      }
    }
  }
  return $changed;
}

function hex_color($c) {
  return sprintf("%02X%02X%02X%02X", $c['red'], $c['green'], $c['blue'], $c['alpha']);
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
