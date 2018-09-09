#!/usr/bin/env php
<?php

// Suffixed items: these appear in the database with a suffix string.
// E.g. the database contains 'red-apricorn', for the icon 'red' in the 'apricorn' set.
$suffix = array(
  'apricorn', 'gem', 'petal', 'shard', 'mulch', 'flute', 'incense', 'memory', 'plate', 'berry', 'ball', 'fossil', 'scarf'
);
// Prefixed items: as above, but suffixed. Only 'roto'.
// E.g. in the database, 'roto-hatch', but in our files, icon 'hatch' in the 'roto' set.
$prefix = array(
  'roto'
);

// Ignored items: these items are expected to not have an icon.
// See <https://github.com/msikma/pokesprite/pull/40#issuecomment-398071404>.
$ignored_items = array(
  // Technical machines.
  'tm00', 'tm01', 'tm02', 'tm03', 'tm04', 'tm05', 'tm06', 'tm07', 'tm08',
  'tm09', 'tm10', 'tm11', 'tm12', 'tm13', 'tm14', 'tm15', 'tm16', 'tm17',
  'tm18', 'tm19', 'tm20', 'tm21', 'tm22', 'tm23', 'tm24', 'tm25', 'tm26',
  'tm27', 'tm28', 'tm29', 'tm30', 'tm31', 'tm32', 'tm33', 'tm34', 'tm35',
  'tm36', 'tm37', 'tm38', 'tm39', 'tm40', 'tm41', 'tm42', 'tm43', 'tm44',
  'tm45', 'tm46', 'tm47', 'tm48', 'tm49', 'tm50', 'tm51', 'tm52', 'tm53',
  'tm54', 'tm55', 'tm56', 'tm57', 'tm58', 'tm59', 'tm60', 'tm61', 'tm62',
  'tm63', 'tm64', 'tm65', 'tm66', 'tm67', 'tm68', 'tm69', 'tm70', 'tm71',
  'tm72', 'tm73', 'tm74', 'tm75', 'tm76', 'tm77', 'tm78', 'tm79', 'tm80',
  'tm81', 'tm82', 'tm83', 'tm84', 'tm85', 'tm86', 'tm87', 'tm88', 'tm89',
  'tm90', 'tm91', 'tm92', 'tm93', 'tm94', 'tm95', 'tm96', 'tm97', 'tm98',
  'tm99', 'tm100',

  // Data cards.
  'data-card-00', 'data-card-01', 'data-card-02', 'data-card-03',
  'data-card-04', 'data-card-05', 'data-card-06', 'data-card-07',
  'data-card-08', 'data-card-09', 'data-card-10', 'data-card-11',
  'data-card-12', 'data-card-13', 'data-card-14', 'data-card-15',
  'data-card-16', 'data-card-17', 'data-card-18', 'data-card-19',
  'data-card-20', 'data-card-21', 'data-card-22', 'data-card-23',
  'data-card-24', 'data-card-25', 'data-card-26', 'data-card-27',
  'data-card-28', 'data-card-29', 'data-card-30',

  // Hidden machines.
  'hm00', 'hm01', 'hm02', 'hm03', 'hm04', 'hm05', 'hm06', 'hm07', 'hm08',
  'hm09', 'hm10',

  // Mega- items are in the database but don't have an icon representation.
  'mega-pendant', 'mega-glasses', 'mega-glove', 'mega-anchor', 'mega-stickpin',
  'mega-tiara', 'mega-anklet',
);

// Typoed items: these items have a typo in the database and need to be redirected
// to the correct file or we'll be unable to find the icon.
// See <https://github.com/msikma/pokesprite/pull/40#issuecomment-398071404>.
$typoed_items = array(
  // The Xtransceiver icons.
  'xtranceiver--red' => 'xtransceiver--red',
  'xtranceiver--yellow' => 'xtransceiver--yellow',
);

// Aliased items: this is for items that share an icon but have a different name.
// I'm not sure if any of these need to have a different icon. It could be that there are
// extra icons that we are missing. Worth trying to check this out sometime.
$aliased_items = array(
  'dna-splicers--merge' => 'dna-splicers',
  'dna-splicers--split' => 'dna-splicers',

  'ss-ticket--hoenn' => 'ss-ticket',

  'n-solarizer--merge' => 'n-solarizer',
  'n-lunarizer--merge' => 'n-lunarizer',
  'n-solarizer--split' => 'n-solarizer',
  'n-lunarizer--split' => 'n-lunarizer',

  'ilimas-normalium-z' => 'normalium-z--bag',
  'left-poke-ball' => 'poke-ball',
);

// Some icons are present in multiple sets. This disambiguates them.
$set_disambiguation = array(
  // 'evo-item' and 'hold-item' have some identical icons.
  'hold-item' => array('evo-item', 'hold-item'),
  // Prefer 'medicine' and 'battle-item' over 'wonder-launcher'.
  'medicine' => array('wonder-launcher', 'medicine'),
  'battle-item' => array('wonder-launcher', 'battle-item'),
);

// We will attempt to match every item from the item export to an icon on the disk.
// Every item that doesn't have a matching icon is reported.
// Every icon that doesn't get used is reported.
$sets = get_icon_sets();
$items = get_item_export();

// Finally, we will generate a new JSON file with the following structure:
// First, a list of icon sets, which we will use when generating the icon spritesheet.
// Secondly, a key-value store of item ID to icon file.
$item_icon_database = array();
$icon_database = array();
$icon_usage = array();

// Run through every item in the database, match it with an icon (if it's not in the ignored item list)
// and save the resulting item/icon object to the final data set.
foreach ($items as $item) {
  $obj = find_item_icon($item, $sets);
  // Save the icon this item will use in the usage array.
  $icon_path = $obj['icon']['set'].'/'.$obj['icon']['filename'];
  $icon_usage[$icon_path] = true;
  // Save the object to the item/icon database.
  // If this item does not have an associated icon, the value will be null instead of a string.
  // If it does have an icon, the string is a reference to an icon ID in 'icons'.
  if ($obj['item']['id']) {
    $item_icon_database[$obj['item']['id']] = !empty($obj['icon']['set']) ? array($obj['icon']['set'], $obj['icon']['filename']) : null;
  }
  if ($obj['icon']['set']) {
    if (!isset($icon_database[$obj['icon']['set']])) {
      $icon_database[$obj['icon']['set']] = array();
    }
    $icon_database[$obj['icon']['set']][$obj['icon']['filename']] = $obj;
  }
}

// Now we need to run through all existing physical icons to see which ones were not linked to an item.
// We will also add these icons to the item/icon database, but without item data.
foreach ($sets as $set_name => $set_icons) {
  foreach ($set_icons as $slug => $icon) {
    $icon_path = $set_name.'/'.$icon['filename'];
    if (isset($icon_usage[$icon_path])) {
      continue;
    }
    // This icon has not been matched with an item.
    $obj = make_item_icon(false, $slug, $set_name, $set_icons[$slug]);
    $icon_usage[$icon_path] = true;

    // Save the object to the item/icon database.
    if (!isset($icon_database[$set_name])) {
      $icon_database[$set_name] = array();
    }
    $icon_database[$set_name][$obj['icon']['filename']] = $obj;
  }
}

// Now output the final file.
file_put_contents('icons.json', json_encode(array('items' => $item_icon_database, 'icons' => $icon_database), JSON_PRETTY_PRINT));
die("Output: './icons.json'. Done.\n");

/**
 * Returns a matching icon file for a specific item.
 * This is highly inefficient and any programmer would hate this code,
 * but it doesn't really matter.
 */
function find_item_icon($item, $sets) {
  global $ignored_items, $typoed_items, $aliased_items, $set_disambiguation;

  // Match on the English language slug.
  $slug = $item['slug']['eng'];

  // Check if we are ignoring this item.
  if (in_array($slug, $ignored_items)) {
    return make_item_icon($item, $slug);
  }

  // Check if the item we're looking for is typoed.
  $is_typoed = false;
  if (isset($typoed_items[$slug])) {
    $slug = $typoed_items[$slug];
    $is_typoed = true;
  }

  // Check if the item is aliased to a different icon file.
  $is_aliased = false;
  if (isset($aliased_items[$slug])) {
    $slug = $aliased_items[$slug];
    $is_aliased = true;
  }

  $matches = array();
  foreach ($sets as $set_name => $set_icons) {
    $set_icon_slugs = array_keys($set_icons);
    if (!in_array($slug, $set_icon_slugs)) {
      continue;
    }
    // Found a match.
    $matches[$set_name] = make_item_icon($item, $slug, $set_name, $set_icons[$slug], $is_typoed, $is_aliased);
  }
  if (count($matches) > 1) {
    $keys = array_keys($matches);
    $disamb = null;
    print('Found '.count($matches).' matches for item #'.$item['id'].' ('.$item['slug']['eng'].'): ');
    foreach ($set_disambiguation as $target => $items) {
      if ($keys != $items) {
        continue;
      }
      $disamb = $target;
    }
    if (!$disamb) {
      print(implode(', ', $keys)." (no disambiguation)\n");
    }
    else {
      print(implode(', ', $keys));
      foreach ($matches as $set_name => $match_obj) {
        if ($set_name === $disamb) {
          print(" (".$disamb.")\n");
          return $match_obj;
        }
      }
      print(" (could not find disambiguation: ".$disamb.")\n");
    }
  }
  return $matches[0];

  // NOTE: we're quitting the script here on error because all items should be accounted for.
  // If one isn't, we need to add it to ignored/aliased/typoed items.
  print("Error: could not find item icon: \n");
  var_dump($item);
  die();
}

/**
 * Returns a new format array with item and icon data.
 */
function make_item_icon($item = false, $slug = null, $set_name = null, $icon = false, $typoed = false, $aliased = false) {
  $icon_data = array();
  if ($item !== false) {
    $icon_data['item'] = array(
      'id' => $item['id'],
      'slug' => $item['slug'],
      'name' => $item['name'],
    );
  }
  if ($typoed !== false) {
    $icon_data['typoed'] = $typoed;
  }
  if ($aliased !== false) {
    $icon_data['aliased'] = $aliased;
  }
  if ($icon !== false) {
    $icon_data['icon'] = array(
      'slug' => $slug,
      'set' => $set_name,
      'filename' => $icon['filename'],
    );
  }
  return $icon_data;
}

/**
 * Returns a list of all icon sets and icons inside: this is based on physical files.
 */
function get_icon_sets() {
  global $suffix, $prefix;

  $sets = array();

  // Iterate through every directory inside 'icons'.
  $root = new DirectoryIterator('./icons/');
  foreach ($root as $set) {
    $set_name = $set->getFilename();
    if ($set->isDot() || $set_name === 'pokemon' || !$set->isDir()) continue;

    if (!isset($sets[$set_name])) {
      $sets[$set_name] = array();
    }

    // Now iterate over all files inside.
    $dir = new DirectoryIterator('./icons/'.$set_name);
    foreach ($dir as $icon) {
      if ($icon->isDot()) continue;

      $filename = $icon->getFilename();
      $icon_name = pathinfo($icon->getFilename(), PATHINFO_FILENAME);

      if (in_array($set_name, $suffix)) {
        $icon_name = $icon_name.'-'.$set_name;
      }
      if (in_array($set_name, $prefix)) {
        $icon_name = $set_name.'-'.$icon_name;
      }

      $sets[$set_name][$icon_name] = array(
        'filename' => $filename
      );
    }
  }

  return $sets;
}

/**
 * Returns the items from the database export.
 */
function get_item_export() {
  $items = json_decode(file_get_contents('./data/item-export.json'), true);
  return $items;
}
