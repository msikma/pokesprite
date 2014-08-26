#!/usr/bin/php
<?php

// PokéSprite
// ----------
// This simple script generates an image sprite containing small icons of
// all Pokémon in the National Pokédex (along with several other types of
// icons), and the pertaining SCSS and JS files. It was built for people
// working on websites or online applications related to Pokémon. The main
// Pokémon icons are arranged sequentially, and a growing packer algorithm
// is used to arrange other images inside the sprite.
//
// Aside from the regular box sprites (which have been sourced from
// Pokémon X/Y), this package includes unofficial custom designed shiny
// versions of those sprites.
//
// The data for the Pokémon icons is taken from the `./data/pkmn.json` file.
// The other icon sets have their data generated based on the directory
// structure and file names.
//
// ---------------------------------------------------------------------------
//
// The MIT License (MIT)
//
// (C) 2014 Michiel Sikma <dada@doubla.de> and PokéSprite contributors
//
// For a full list of contributors, view the project commit history.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the "Software"),
// to deal in the Software without restriction, including without limitation
// the rights to use, copy, modify, merge, publish, distribute, sublicense,
// and/or sell copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
// THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
// DEALINGS IN THE SOFTWARE.
// 
// ---------------------------------------------------------------------------
//
// All files in the `icons` directory are included in this package for reasons
// of convenience and are (C) 1995-2014 Nintendo/Creatures Inc./GAME FREAK Inc.

namespace PkSpr;

require_once 'includes/templateformatter.php';
require_once 'includes/terminalformatter.php';
require_once 'includes/settings.php';
require_once 'includes/scaffolding.php';
require_once 'includes/i18n.php';
require_once 'includes/usage.php';
require_once 'includes/iconstack.php';
require_once 'includes/functions.php';
require_once 'includes/iconoverview.php';
require_once 'includes/iconstyler.php';
require_once 'includes/iconjs.php';
require_once 'includes/iconsprite.php';

// 1. Parsing of command line arguments and basic program feedback
// ---------------------------------------------------------------------------

// Load the default settings.
Settings::load_settings_file('includes/defaults.php');

// Parse the user's command line arguments and determine whether the user
// is in need of seeing the usage guide.
$usage = new Usage();
$cl_settings = $usage->get_user_settings();

// Check the current revision number for user feedback and for
// inclusion in the output files.
$revision = trim(shell_exec('git rev-list HEAD --count 2> /dev/null'));
$revision = $revision ? $revision : '[unknown]';
$cl_settings['revision'] = $revision;

if ($usage->needs_usage) {
    // Display usage and exit.
    $usage->load_tpl_file(
        Settings::get('resources_dir').
        Settings::get('usage_tpl')
    );
    $usage->display_usage();
    exit;
}
// Merge the user's command line arguments into the settings.
Settings::load_settings($cl_settings);

// Export the settings variables so that we can use them here.
$vars = array_keys(Settings::$settings);
foreach ($vars as $var) {
    $$var = Settings::get($var);
}

// Set the default lines.
I18n::set_language($i18n_language);
I18n::set_default_lines();
$termfrm = new TerminalFormatter();
I18n::add_output_filter(array('PkSpr\TerminalFormatter', 'format'));

// Print basic program info.
print(I18n::lf('info', array(
    $title_str,
    $revision,
    $website_txt,
    $copyright_gf
)));
$generated_on = I18n::lf('generated_on', array($script_date));

// Check if we're running via anything other than the command line.
if (php_sapi_name() != 'cli') {
    print(I18n::l('no_cli'));
}

// Check if pngcrush exists, in case we need it.
if ($generate_optimized === true) {
    if (!is_file($pngcrush_path)) {
        print(I18n::lf('pngcrush_missing', array($pngcrush_path)));
        $generate_optimized = false;
    }
}

// Check if the output directory exist and create them if necessary.
if (!is_dir($dir_output)) {
    print(I18n::l('dir_create'));
    if (!mkdir($dir_output, $dir_mode, true)) {
        print(I18n::l('dir_error'));
        die();
    }
    print(I18n::l('dir_success'));
}

// Print a quick overview of what we'll be doing.
print(I18n::l('tasks_overview'));
if ($include_pkmn) {
    print(I18n::lf('task_pkmn', array(
        $include_pkmn ? 'true' : 'false',
        $include_pkmn_nonshiny ? 'true' : 'false',
        $include_pkmn_shiny ? 'true' : 'false',
        $include_pkmn_forms ? 'true' : 'false',
        $include_pkmn_right,
        $pkmn_language
    )));
}
if ($include_icon_sets) {
    print(I18n::l('task_icon_sets'));
}
if ($generate_optimized) {
    print(I18n::l('task_optimize'));
}
print(I18n::l('task_html'));
if ($generate_markdown) {
    print(I18n::l('task_markdown'));
}
print(I18n::l('task_scss'));
print(I18n::l('task_js'));

// 2. Pokémon data parsing and icon stack generation
// ---------------------------------------------------------------------------

$icon_stack = new IconStack();

// Retrieve Pokémon name and idx information from the data file.
if ($include_pkmn) {
    $icon_stack->parse_data_file($dir_data.$file_pkmn_data, $pkmn_range);
    $pkmn_data = $icon_stack->get_pkmn_data();
    
    // Check if the data is there.
    $has_data = $icon_stack->has_pkmn_data();
    $has_imgs = $icon_stack->has_pkmn_images();
    
    if (!$has_data) {
        print(I18n::lf('no_data', array($file_pkmn_data, $dir_data)));
        die();
    }
    if (!$has_imgs) {
        print(I18n::l('no_images'));
        die();
    }
}
else {
    // If we aren't including Pokémon, keep an empty array
    // to avoid iteration warnings.
    $pkmn_data = array();
}

// Generate and return the entire Pokémon icon stack.
$pkmn_icons = $icon_stack->get_pkmn_icon_stack();
$pkmn_std_icons = $icon_stack->get_pkmn_std_icons();

// Get the total size of the Pokémon icons. We'll position the rest of the
// images using the same width, and an arbitrary height.
$pkmn_sect_fit = $icon_stack->get_pkmn_icon_stack_size();

// We'll keep images other than Pokémon icons inside of a separate array.
// Position them using a binary tree packing algorithm.
$etc_icons = $icon_stack->get_etc_icon_stack();
$etc_sets = $icon_stack->get_etc_icon_sets();
$etc_sect_fit = $icon_stack->get_etc_icon_stack_size();

// Full sprite attributes
$stack_size = $icon_stack->get_combined_stack_size();
$sprite_width = $stack_size['w'];
$sprite_height = $stack_size['h'];

// Now we'll generate the sprite sheet.
// About how much memory will this use?
$mb_estimate = format_bytes($sprite_width * $sprite_height * 32);
print(I18n::lf('sprite_stats', array(
    $sprite_width,
    $sprite_height,
    count($etc_icons) + count($pkmn_icons),
    $mb_estimate,
)));

// 3. Generation of the icon sprite
// ---------------------------------------------------------------------------

$sprite_sections = array(
    'pkmn' => $pkmn_sect_fit['h'],
    'etc' => $etc_sect_fit['h'],
);
$icon_sprite = new IconSprite(
    $sprite_width,
    $sprite_height,
    $sprite_sections,
    $debug_img_inclusion
);
// Pass on data on which icons are the standard variants.
$icon_sprite->set_pkmn_std_icons($pkmn_std_icons);

// Array of both the Pokémon icons and the other icons.
$all_icons = $icon_stack->get_all_icons();

// For the sprite image itself, iterate over all icons and add them.
$added = 0;
$total = count($all_icons);
for ($a = 0; $a < count($all_icons); ++$a) {
    // IconSprite returns true if successful, false if the image
    // could not be used somehow.
    $icon = $all_icons[$a];
    if ($icon_sprite->add($icon)) {
        $added += 1;
    }
}
print(I18n::lf('icons_added', array($added)));

// Send some feedback in case we couldn't add all icons.
if (($added - $total) > 0) {
    if ($debug_img_inclusion) {
        print(I18n::lf('icons_skipped', array($added - $total)));
    }
    else {
        print(I18n::lf('icons_skipped_hint', array($added - $total)));
    }
}

print(I18n::l('sprite_ready'));
print(I18n::lf('sprite_saving', array(
    $generate_optimized ? $dir_output.$img_output_tmp : $dir_output.$img_output,
)));

// Save the initial result to output directory.
if (file_exists($dir_output.$img_output_tmp)) {
    unlink($dir_output.$img_output_tmp);
}
$icon_sprite->output($dir_output.$img_output_tmp, $generate_optimized !== true);
$icon_sprite->destroy();

// Use pngcrush to minimize the image.
if ($generate_optimized === true) {
    print(I18n::l('pngcrush_start'));
    if (file_exists($dir_output.$img_output)) {
        unlink($dir_output.$img_output);
    }
    $crush_cmd = $pngcrush_path.' -l 9 -q -text b author "Pokémon Sprite Generator r'.$revision.'" -text b copyright "'.$copyright_gf.'" '.$dir_output.$img_output_tmp.' '.$dir_output.$img_output;
    exec($crush_cmd);

    if (file_exists($dir_output.$img_output)) {
        // Seems like pngcrush was successful. Let's review the results.
        $size_before = filesize($dir_output.$img_output_tmp);
        $size_after = filesize($dir_output.$img_output);
        print(I18n::lf('pngcrush_success', array(
            format_bytes($size_before),
            format_bytes($size_after),
        )));
        // Delete the unoptimized image.
        print(I18n::l('sprite_del_old'));
        unlink($dir_output.$img_output_tmp);
    }
    else {
        // We couldn't use pngcrush for some reason.
        print(I18n::l('pngcrush_error'));
        exit;
    }
}
else {
    // If we're not using pngcrush, just rename it to the final filename.
    rename($dir_output.$img_output_tmp, $dir_output.$img_output);
}

// 4. Generation of the HTML, SCSS and JS files pertaining to the sprites
// ---------------------------------------------------------------------------

// We're going to be making a map of positioning values, styling information
// as well as an HTML overview of every icon.
$icon_overview = new IconOverview();
$icon_styler = new IconStyler();
$icon_js = new IconJS();

// Create a tree structure of our icon sets based on their subtype information.
// This will be given to the SCSS, JS and overview generators.
$icon_tree = $icon_stack->get_icon_type_tree();
$set_sizes = $icon_stack->get_set_sizes();
$icon_overview->set_icon_list($all_icons);
$icon_styler->set_icon_list($all_icons);
$icon_styler->set_icon_sizes($set_sizes);
$icon_js->set_sprite_sections($sprite_sections);
$icon_js->set_icon_tree($icon_tree);
$icon_js->set_icon_sizes($set_sizes);

// For the generation of the HTML, SCSS and JS files, a number of
// base variables are used.
$base_vars = array(
    'title_str' => $title_str,
    'revision' => $revision,
    'website_txt' => $website_txt,
    'copyright_str' => $copyright_str,
    'copyright_gf' => $copyright_gf,
    'copyright_contrib_notice' => $copyright_contrib_notice,
    'generated_on' => $generated_on,
    'css_base_selector' => $css_base_selector,
);

// Generate the HTML overview of all icons we've just added.
print(I18n::l('html_generating'));
$icon_overview->register_vars($base_vars);
$icon_overview->register_tpl($resources_dir.$html_tpl);
$overview = $icon_overview->get_overview('html');
if (file_exists($dir_output.$html_output)) {
    unlink($dir_output.$html_output);
}
file_put_contents($dir_output.$html_output, $overview);

// If requested, generate a Markdown overview too.
if ($generate_markdown) {
    print(I18n::l('markdown_generating'));
    $overview_md = $icon_overview->get_overview('markdown');
    if (file_exists($dir_output.$md_output)) {
        unlink($dir_output.$md_output);
    }
    file_put_contents($dir_output.$md_output, $overview_md);
}

// With the image and the overview done, let's generate the SCSS.
print(I18n::l('scss_generating'));
$icon_styler->register_vars($base_vars);
$icon_styler->register_tpl($resources_dir.$scss_tpl);
$scss = $icon_styler->get_scss();
if (file_exists($dir_output.$scss_output)) {
    unlink($dir_output.$scss_output);
}
file_put_contents($dir_output.$scss_output, $scss);

// Finally, the JS.
print(I18n::l('js_generating'));
$icon_js->register_vars($base_vars);
$icon_js->register_tpl($resources_dir.$js_tpl);
$js = $icon_js->get_js();
if (file_exists($dir_output.$js_output)) {
    unlink($dir_output.$js_output);
}
file_put_contents($dir_output.$js_output, $js);

// All done!
print(I18n::l('all_done'));
exit;
