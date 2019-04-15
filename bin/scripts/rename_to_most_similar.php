#!/usr/bin/env php
<?php

# rename_to_most_similar.php: used to rename the images from a new dump
# to the filenames (and paths) of an old dump.
#
# Use with care, overwrites files.
#
# This script requires libpuzzle to be compiled and present.
# See <https://github.com/jedisct1/libpuzzle> for more information.

set_time_limit(0);

$verbose = false;
$show_hits = true;
$dir_old = @$argv[1];
$dir_new = @$argv[2];
$treshold = 0.09;
$GLOBALS['file_exts'] = array('jpg', 'png', 'jpeg', 'gif');

if (!isset($dir_old) || !isset($dir_new)) {
  print('usage: rename_to_most_similar.php old_dir new_dir'.PHP_EOL.'rename_to_most_similar.php: error: too few arguments'.PHP_EOL);
  exit();
}

if (!is_dir($dir_old) || !is_dir($dir_new)) {
  print('usage: rename_to_most_similar.php old_dir new_dir'.PHP_EOL.'rename_to_most_similarrename_to_most_similar.php: error: old_dir or new_dir aren\'t directories'.PHP_EOL);
  exit();
}

$imgs_old = iterate_dir($dir_old);
$imgs_new = iterate_dir($dir_new);
$renamed_imgs = 0;

overwrite_clean_imgs($imgs_old);
die('z');

$report = '<table border="2"><tr><th>New</th><th>Old</th></tr>';

$unique_new_imgs = array();

$counter = 0;
$total = count($imgs_old) * count($imgs_new);

print('old: `'.$dir_old.'\' contains '.count($imgs_old).' image files.'.PHP_EOL);
print('new: `'.$dir_new.'\' contains '.count($imgs_new).' image files.'.PHP_EOL);
print(PHP_EOL.'We will make '.(count($imgs_old) * count($imgs_new)).' comparisons.'.PHP_EOL);

if ($verbose) {
  print(PHP_EOL);
}
$counter_new = 0;
foreach ($imgs_new as $img_new_path => $img_new_info) {
  $best_match = array('diff' => 1, 'dir' => '', 'path' => '');
  foreach ($imgs_old as $img_old_path => $img_old_info) {
    $diff = get_img_diff($img_new_path, $img_old_path);
    if ($verbose || $show_hits) {
      $perc = ($counter / $total);
      if ($perc >= 1) {
        $perc = ' 100%';
      }
      if ($perc < 1) {
        $perc = sprintf('%04.1f%%', $perc * 100);
      }
    }
    if ($verbose) {
      print('['.$perc.'] comparing: `'.$img_new_path.'\' to `'.$img_old_path.'\': diff: '.$diff.PHP_EOL);
    }
    $counter += 1;
    
    if ($diff <= $treshold && $diff < $best_match['diff']) {
      // Found a better match.
      $best_match['diff'] = $diff;
      $best_match['path'] = $img_old_path;
      $best_match['dir'] = $img_old_info['dir'];
    }
  }
  if ($best_match['diff'] <= $treshold && $show_hits) {
    $report .= '<tr><td class="new">'.$img_new_tag.'</td><td class="old">'.$img_old_tag.'</td></tr>';
    print('['.$perc.'] renaming `'..'\' to `'..'\' (trehold: '.$treshold.')');
    rename_img($img_new_path, $best_match);
    $renamed_imgs += 1;
  }
  $counter_new += 1;
}

if ($verbose || $show_hits) {
  print('[ 100%] done.'.PHP_EOL);
}
print(PHP_EOL.'Amount of renamed images: '.$renamed_imgs);
print(PHP_EOL);

function rename_img($new_path, $old_info)
{
  var_dump($new_path);
  print_r($old_info);
}

function overwrite_clean_imgs($imgs)
{
  foreach ($imgs as $img => $info) {
    print_r($info);
  }
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
    $stack[$file_path] = array('dir' => $dir.'/');
  }
  return $stack;
}

function get_img_diff($new, $old)
{
  $diff = trim(exec('puzzle-diff "'.$new.'" "'.$old.'" 2>&1', $output, $code));
  if ($code !== 0) {
    print(PHP_EOL.'rename_to_most_similar.php: error: couldn\'t run the `puzzle-diff\' script'.PHP_EOL);
    die();
  }
  return floatval($diff);
}