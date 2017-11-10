<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

// These are the default settings. They should not be modified.
// If you want to use different settings, type: ./pokesprite.php --help

$s = array();

// Include Pokémon sprites
$s['include_pkmn'] = true;
// Include regular (non-shiny) sprites
$s['include_pkmn_nonshiny'] = true;
// Include (unofficial) shiny sprites
$s['include_pkmn_shiny'] = true;
// Include forms and gender differences
$s['include_pkmn_forms'] = true;
// Include right-facing sprites (0 = None, 1 = Only unique icons, 2 = All)
$s['include_pkmn_right'] = 1;
// Include the icon sets
$s['include_icon_sets'] = true;
// Include the special icons (egg, unknown Pokémon)
$s['include_special_icons'] = true;

// Language used for Pokémon names
$s['pkmn_language'] = 'eng';
// Language used for Pokémon slugs
$s['pkmn_language_slugs'] = 'eng';
// Language used by the program
$s['i18n_language'] = 'en_us';
// Slug language of the icon files (should not be changed)
$s['img_slug_lang'] = 'eng';

// Copyright strings
$s['copy_a'] = '2014';
$s['copy_z'] = date('Y');
$s['title_str'] = 'PokéSprite';
$s['revision'] = '[unknown]';
$s['website'] = 'https://github.com/msikma/pokesprite';
$s['website_txt'] = '<'.$s['website'].'>';
$s['main_contributor'] = 'Michiel Sikma <michiel@sikma.org>';
$s['other_contributors'] = 'PokéSprite contributors';
$s['contributors'] = implode(' and ', array($s['main_contributor'], $s['other_contributors']));
$s['copyright_str'] = '(C) '.($s['copy_a'] != $s['copy_z'] ? $s['copy_a'].'-'.$s['copy_z'] : $s['copy_a']).', '.$s['contributors'];
$s['copyright_gf'] = '(C) 1995-'.$s['copy_z'].' Nintendo/Creatures Inc./GAME FREAK Inc.';
$s['copyright_contrib_notice'] = 'For a full list of contributors, view the project commit history.';
$s['script_date'] = date('Y-m-d H:i:s');

// Base URL for direct links to icon files.
$s['icon_url_info_base'] = 'https://github.com/msikma/pokesprite/tree/master/';
$s['icon_url_img_base'] = 'https://raw.githubusercontent.com/msikma/pokesprite/master/';

// Icons base directory
$s['dir_base'] = './icons/';
// Pokémon sprites base directory
$s['dir_pkmn'] = 'pokemon/';
// Regular sprites directory
$s['dir_pkmn_regular'] = 'regular/';
// Shiny sprites directory
$s['dir_pkmn_shiny'] = 'shiny/';
// Special sprites directory
$s['dir_pkmn_special'] = '';
// Female sprites directory
$s['dir_pkmn_female'] = 'female/';
// Right-facing sprites directory
$s['dir_pkmn_right'] = 'right/';
// Permitted extensions for images
$s['file_exts'] = array('png');
// Data directory
$s['dir_data'] = './data/';
// File containing Pokémon data
$s['file_pkmn_data'] = 'pkmn.json';
// Output directory (created if nonexistent)
$s['dir_output'] = './output/';
// Mode at which the directory is created
$s['dir_mode'] = 0777;
// If set, which Pokémon to include (debugging only)
$s['pkmn_range'] = null;
// Whether to index numbers instead of slugs in the overview (debugging only)
$s['html_no_slugs'] = false;
// Github project base directory
$s['github_base_dir'] = 'https://raw.github.com/msikma/pokesprite/master/';

// List of icon sets that are to be included aside from the Pokémon icons.
// This searches for the directory in the icons base directory.
// If all images have the same size, their width and height is set only
// once in the SCSS code for efficiency.
$s['etc_icon_sets'] = array(
    'apricorn', 'battle-item', 'berry', 'body-style', 'etc', 'ev-item',
    'evo-item', 'flute', 'fossil', 'gem', 'hm', 'hold-item', 'incense',
    'other-item', 'key-item', 'mail', 'medicine', 'mega-stone', 'mulch',
    'plate', 'pokeball', 'scarf', 'shard', 'tm', 'valuable-item',
    'wonder-launcher', 'z-crystals', 'memory', 'roto', 'petal'
);

// Pokémon icon width
$s['pkmn_img_width'] = 40;
// Pokémon icon height
$s['pkmn_img_height'] = 30;
// Pokémon icon padding
$s['pkmn_img_padding'] = 1;
// Amount of icons in one row of the sprite
$s['pkmn_row_count'] = 32;

// Print feedback on image inclusion
$s['debug_img_inclusion'] = false;
// Display colors in feedback
$s['debug_monochrome'] = false;

// Whether to pngcrush the resulting image
$s['generate_optimized'] = true;
// Temporary image file name
$s['img_output_tmp'] = 'pokesprite_tmp.png';
// Output name of the image
$s['img_output'] = 'pokesprite.png';
// Output name of the SCSS file
$s['scss_output'] = 'pokesprite.scss';
// Output name of the JS file
$s['js_output'] = 'pokesprite.js';
// Output name of the HTML overview
$s['html_output'] = 'overview.html';
// Output name of the HTML file with build information
$s['html_build_output'] = 'files.html';
// Output name of the Markdown overview
$s['md_output'] = 'overview.md';
// Resources directory name
$s['resources_dir'] = './resources/';
// SCSS template file
$s['scss_tpl'] = 'pokesprite-tpl.scss';
// JS template file
$s['js_tpl'] = 'pokesprite-tpl.js';
// HTML template file
$s['html_tpl'] = 'overview-tpl.html';
// HTML template file for the build information
$s['html_build_tpl'] = 'files-tpl.html';
// Relative link back to the overview for the docs
$s['html_rel_home'] = '/pokesprite/';
// Usage template file
$s['usage_tpl'] = 'usage.tpl';
// Version template file
$s['version_tpl'] = 'version.tpl';
// Whether to generate a Markdown overview
$s['generate_markdown'] = false;


// Base CSS selector (identifies an element as ours)
$s['css_base_selector'] = 'pkspr';
// Base SCSS/JS variable name
$s['var_base_name'] = 'pkspr';

// External tools
$s['pngcrush_path'] = './tools/pngcrush';
$s['sass_path'] = 'sass';
$s['closure_path'] = 'wipwipwip';
