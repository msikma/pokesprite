<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

require_once 'includes/growingpacker.php';

/**
 * Class that parses the Pokémon data and analyzes it.
 */
class IconStack
{
    /** @var mixed[] Pokémon data. */
    public $pkmn_data = array();
    
    /** @var int Pokémon sprite image width. */
    public $pkmn_img_width;
    /** @var int Pokémon sprite image height. */
    public $pkmn_img_height;
    /** @var int Amount of sprite images in one row. */
    public $pkmn_row_count;
    
    /** @var string[] Sprite versions to include (regular, shiny). */
    public $versions = array();
    
    /** @var int Stack counter. */
    private $counter = 0;
    /** @var mixed[] Pokémon sprite stack. */
    public $pkmn_stack = array();
    /** @var mixed[] Default sprites (base for a duplicate). */
    public $std_sprites = array();
    /** @var mixed[] Etc sprite stack (all non-Pokémon box sprite icons). */
    public $etc_stack = array();
    /** @var mixed[] Etc sprite sets. */
    public $etc_sets = array();
    /** @var int Etc sprite stack width. */
    public $etc_sect_width = 0;
    /** @var int Etc sprite stack height. */
    public $etc_sect_height = 0;
    
    /** @var mixed[] Icon set sizes. */
    public $set_sizes = array();
    /** @var mixed[] Icon data. */
    public $type_tree = array();
    
    /**
     * Initializes a number of variables from the settings file.
     */
    public function __construct()
    {
        $vars = array('pkmn_img_width', 'pkmn_img_height', 'pkmn_row_count');
        foreach ($vars as $var) {
            $this->$var = Settings::get($var);
        }
    }
    
    /**
     * Get icon stack of all icons besides Pokémon sprites.
     *
     * @return mixed[] Etc icon stack data.
     */
    public function get_etc_icon_stack()
    {
        if (empty($this->etc_stack)) {
            $this->create_etc_icon_stack();
        }
        return $this->etc_stack;
    }
    
    /**
     * Get all icons.
     *
     * @return mixed[] Icon data.
     */
    public function get_all_icons()
    {
        return array_merge($this->pkmn_stack, $this->etc_stack);
    }
    
    /**
     * Get icon set info.
     *
     * @return mixed[] Etc icon set data.
     */
    public function get_etc_icon_sets()
    {
        if (empty($this->etc_sets)) {
            $this->create_etc_icon_stack();
        }
        return $this->etc_sets;
    }
    
    /**
     * Creates a special type of data structure specially suited
     * for the SCSS styler, JS generator and overview generator.
     */
    public function create_icon_type_tree()
    {
        $icons = $this->get_all_icons();
        $pkmn_sect_size = $this->get_pkmn_icon_stack_size();
        
        $tree = array();
        foreach ($icons as $icon) {
            $subvariation = $icon['subvariation'];
            $subvariation = isset($subvariation) ? $subvariation : '.';
            $type = $icon['type'];
            $idx = $icon['idx'];
            $set = @$icon['set'];
            $original = @$icon['original'];
            
            $slug = $icon['slug'];
            $variation = $icon['variation'];
            $version = $icon['version'];
        
            $is_standard = $variation == '.';
            
            // All icons other than Pokémon sprites need to be offset
            // with the height of the Pokémon sprite section.
            $x = $icon['fit']['x'];
            $y = $icon['fit']['y'];
            if ($type != 'pkmn') {
                $y += $pkmn_sect_size['h'];
            }
            
            $icon_info = array(
                'std' => $is_standard,
                'w' => $icon['w'],
                'h' => $icon['h'],
                'x' => $x,
                'y' => $y,
                'type' => $type,
                'original' => $original,
                'set' => $set,
                'idx' => $idx,
                'id' => $icon['id'],
            );
        
            if ($type == 'pkmn') {
                $tree['pkmn'][$slug][$variation][$version][$subvariation] = $icon_info;
            }
            else {
                $tree['etc'][$set][$slug] = $icon_info;
            }
        }
        $this->type_tree = $tree;
    }
    
    /**
     * Returns the type tree data structure for our icons.
     *
     * @return mixed[] Pokémon icon type tree data.
     */
    public function get_icon_type_tree()
    {
        if (empty($this->type_tree)) {
            $this->create_icon_type_tree();
        }
        return $this->type_tree;
    }
    
    /**
     * Determines and returns the sizes of the Pokémon icon sets.
     *
     * @return mixed[] Pokémon icon set sizes.
     */
    public function get_set_sizes()
    {
        if (!empty($this->set_sizes)) {
            return $this->set_sizes;
        }
        $sizes = array();
        
        // The Pokémon icon sizes are always static.
        $sizes['pkmn'] = array(
            'w' => Settings::get('pkmn_img_width'),
            'h' => Settings::get('pkmn_img_height'),
        );
        // Define the sizes for other items.
        if (empty($this->type_tree['etc'])) {
            $this->type_tree['etc'] = array();
        }
        foreach ($this->type_tree['etc'] as $slug => $icons) {
            $w = 0;
            $h = 0;
            foreach ($icons as $icon) {
                // Define the size if we're at the first iteration.
                // Continue if it's the same.
                // If a different size is found than the one that
                // was defined before, this section will be determined
                // to have variable image sizes.
                $same = ($w === $icon['w'] && $h === $icon['h']);
                $zero = ($w === 0 && $h === 0);
                if ($same) {
                    continue;
                }
                if ($zero) {
                    $w = $icon['w'];
                    $h = $icon['h'];
                }
                else {
                    // Images have variable sizes. Save an empty array only.
                    $sizes[$slug] = array();
                    continue(2);
                }
            }
            $sizes[$slug] = array('w' => $w, 'h' => $h);
        }
        $this->set_sizes = $sizes;
        return $this->set_sizes;
    }
    
    /**
     * Creates an icon stack of the etc sprites.
     */
    private function create_etc_icon_stack()
    {
        // This stack will contain icons from all other icon sets.
        $stack = array();
        
        // Save an array of sets.
        $sets = array();
        
        $etc_icon_sets = Settings::get('etc_icon_sets');
        $dir_base = Settings::get('dir_base');
        $file_exts = Settings::get('file_exts');
        
        // Start off where the Pokémon stack ended.
        $n = count($this->pkmn_stack);
        
        if (Settings::get('include_icon_sets')) {
            foreach ($etc_icon_sets as $set) {
                $dir = $dir_base.$set.'/';
                try {
                    $dir_it = new \DirectoryIterator($dir);
                } catch (Exception $e) {
                    print(I18n::lf('icon_dir_failure', array($dir)));
                    continue;
                }
                foreach ($dir_it as $file) {
                    // Some checks to ensure it's a valid image.
                    if ($file->isDot()) {
                      continue;
                    }
                    if ($file->isDir()) {
                      continue;
                    }
                    $fn = $file->getFilename();
                    $fn_bits = explode('.', $fn);
                    $fn_ext = strtolower(trim(end($fn_bits)));
                    if (!in_array($fn_ext, $file_exts)) {
                        continue;
                    }
                    $size = getimagesize($dir.$fn);
                    $fn_slug = slugify(implode('.', array_slice($fn_bits, 0, -1)));
                    $var = $this->get_icon_var_name($set, $fn_slug);
                    $n += 1;
                    $stack[$n] = array(
                        'type' => 'etc',
                        'var' => $var,
                        'set' => $set,
                        'section' => 'other',
                        'id' => $n,
                        'slug' => $fn_slug,
                        'w' => $size[0],
                        'h' => $size[1],
                        'file' => $dir.$fn,
                    );
                    $sets[$set][] = $n;
                }
            }
        }

        // Sort icons by size.
        uasort($stack, array($this, 'etc_max_w_h_sort'));
        
        // Deep convert $stack to a StdClass rather than a regular array.
        // This is in order to be able to use it with the GrowingPacker, which
        // only accepts a StdClass as input.
        $stack = json_decode(json_encode($stack), false);
        
        // Make sure to pass the width of the Pokémon section as
        // the initial width. If we're not including Pokémon icons,
        // set it to a static value.
        $pkmn_sect_size = $this->get_pkmn_icon_stack_size();
        $initial_width = @$pkmn_sect_size['w'];
        if (!$initial_width) {
            $initial_width = $this->pkmn_img_width * $this->pkmn_row_count;
        }
        
        // Initialize our packing algorithm and feed the icons.
        // Permit horizontal growth only if we're not including Pokémon icons.
        $packer = new GrowingPacker();
        $packer->fit($stack, $initial_width, null, false, true);
        
        // Convert back to array.
        $stack = json_decode(json_encode($stack), true);
        
        // Remove extraneous data from the stack.
        foreach ($stack as $n => $icon) {
            unset($stack[$n]['fit']['right']);
            unset($stack[$n]['fit']['down']);
            unset($stack[$n]['fit']['used']);
        }
        
        $this->etc_stack = $stack;
        $this->etc_sets = $sets;
        $this->etc_sect_width = intval($packer->root->w);
        $this->etc_sect_height = intval($packer->root->h);
    }
    
    /**
     * Sort by size (descending), then id (ascending).
     *
     * This is used to prepare data for use with the GrowingPacker class,
     * which works best when the input data is sorted by max(width, height).
     * In cases of identical size, we sort by the id number to keep things of
     * the same size in their originally intended order.
     *
     * @param mixed $a Left comparison.
     * @param mixed $b Right comparison.
     */
    private function etc_max_w_h_sort($a, $b)
    {
        // Take either the width or the height, whichever is the largest.
        $a_size = max($a['w'], $a['h']);
        $b_size = max($b['w'], $b['h']);
    
        // If the items have the same size,
        if ($a_size == $b_size) {
            // Check their series number instead.
            $a_id = $a['id'];
            $b_id = $b['id'];
        
            if ($a_id == $b_id) {
                return 0;
            }
            // Ascending
            return ($a_id < $b_id) ? -1 : 1;
        }
        // Descending
        return ($a_size > $b_size) ? -1 : 1;
    }
    
    /**
     * Sorts the icons so that the standard variation is always at the top.
     *
     * This is necessary to properly link duplicates to the original icon.
     * Must be used with uksort().
     *
     * @param mixed $a Left comparison.
     * @param mixed $b Right comparison.
     */
    private function pkmn_variation_sort($a, $b)
    {
        // '.' must always go above the rest.
        $val = $a == '.' ? -1 : ($b == '.' ? 1 : 0);
        if ($val == 0) {
            // In all other cases, use alphabetic order.
            return ($a < $b) ? -1 : 1;
        }
        else {
            return $val;
        }
    }
    
    /**
     * Parse Pokémon data file.
     *
     * @param string $file Filename.
     * @param string $range Range variable (undocumented; debugging only).
     */
    public function parse_data_file($file, $range=null)
    {
        $this->pkmn_data = json_decode(file_get_contents($file), true);
        
        // If requested, process only a specific range. (Debugging variable.)
        if (isset($range)) {
            $range = str_replace(',', '-', $range);
            $range = explode('-', $range);
            if ($range[1] <= $range[0]) {
                $range[1] = $range[0];
            }
            $range = array(
                intval($range[0]) - 1,
                intval($range[1]) - intval($range[0]) + 1
            );
            $this->pkmn_data = array_slice(
                $this->pkmn_data,
                $range[0],
                $range[1]
            );
        }
    }
    
    /**
     * Checks whether Pokémon data is loaded and non-empty.
     */
    public function has_pkmn_data()
    {
        return !empty($this->pkmn_data);
    }
    
    /**
     * Checks whether Pokémon images can be found.
     */
    public function has_pkmn_images()
    {
        // Check to see if the images are there.
        $test_img = @reset($this->pkmn_data);
        $final_img = (
            Settings::get('dir_base').
            Settings::get('dir_pkmn').
            Settings::get('dir_pkmn_regular').
            $test_img['slug'][Settings::get('img_slug_lang')].
            '.png'
        );
        return is_file($final_img);
    }
    
    /**
     * Returns parsed Pokémon data.
     *
     * @return mixed[] Parsed Pokémon data.
     */
    public function get_pkmn_data()
    {
        return $this->pkmn_data;
    }
    
    /**
     * Returns x/y coordinates for the next Pokémon icon.
     *
     * @param int $inc Whether to increment the counter.
     * @return mixed[] Coordinates.
     */
    private function get_pkmn_icon_fit($inc=true)
    {
        $width = $this->pkmn_img_width;
        $height = $this->pkmn_img_height;
        $row_count = $this->pkmn_row_count;
        
        if ($inc != false) {
            $this->counter += 1;
        }
        $x = ($this->counter % $row_count) * $width;
        $y = floor($this->counter / $row_count) * $height;
        $fit = array(
            'fit' => array(
                'x' => $x,
                'y' => $y,
            ),
        );
        return $fit;
    }
    
    /**
     * Returns the total size of the Pokémon icon stack.
     *
     * @return int[] Pokémon icon stack size.
     */
    public function get_pkmn_icon_stack_size()
    {
        if (!empty($this->pkmn_sect_width)) {
            return array(
                'w' => $this->pkmn_sect_width,
                'h' => $this->pkmn_sect_height,
            );
        }
        $fit = $this->get_pkmn_icon_fit(false);
    
        if ($this->counter > $this->pkmn_row_count) {
            $width = ($this->pkmn_img_width * $this->pkmn_row_count);
        }
        else {
            $width = $fit['fit']['x'] + $this->pkmn_img_width;
        }
        
        $height = $fit['fit']['y'] + $this->pkmn_img_height;
        
        $this->pkmn_sect_width = intval($width);
        $this->pkmn_sect_height = intval($height);
        
        return array(
            'w' => $this->pkmn_sect_width,
            'h' => $this->pkmn_sect_height,
        );
    }
    
    /**
     * Returns the total size of the etc icon stack.
     *
     * @return int[] Etc icon stack size.
     */
    public function get_etc_icon_stack_size()
    {
        return array(
            'w' => $this->etc_sect_width,
            'h' => $this->etc_sect_height,
        );
    }
    
    /**
     * Returns the total size of the Pokémon stack plus the etc icon
     * stack below it.
     *
     * @return int[] Full icon stack size.
     */
    public function get_combined_stack_size()
    {
        $include_pkmn = Settings::get('include_pkmn');
        $include_icon_sets = Settings::get('include_icon_sets');
        
        $pkmn_size = $this->get_pkmn_icon_stack_size();
        $etc_size = $this->get_etc_icon_stack_size();
        
        $width = $include_pkmn ? $pkmn_size['w'] : $etc_size['w'];
        $height = $include_pkmn ? $pkmn_size['h'] : 0;
        $height += $include_icon_sets ? $etc_size['h'] : 0;
        
        return array(
            'w' => $width,
            'h' => $height,
        );
    }
    
    /**
     * Creates an icon stack of the Pokémon sprites.
     *
     * This stack can then be fed to a number of other systems,
     * including the sprite image and SCSS/JS generators.
     */
    public function create_pkmn_icon_stack()
    {
        // Loop through the available Pokémon and adding each of their
        // variants and forms to a stack.
        
        // Initialize the sprite stack.
        $this->pkmn_stack = array();
        
        // List of standard sprite variants.
        $this->std_sprites = array();
        
        // Include regular Pokémon, and shiny Pokémon if set.
        $this->versions = array();
        if (Settings::get('include_pkmn_nonshiny')) {
            $this->versions[] = 'regular';
        }
        if (Settings::get('include_pkmn_shiny')) {
            $this->versions[] = 'shiny';
        }
        $this->counter = -1;
        
        // If we're skipping Pokémon sprite icons, $pkmn_data will be empty.
        foreach ($this->pkmn_data as $id => $pkmn) {
            $stack_items = $this->get_pkmn_stack_items($id, $pkmn);
            foreach ($stack_items as $item) {
                $this->pkmn_stack[] = $item;
            }
        }
        
        // Add the special items (e.g. egg, unknown).
        $special_items = $this->get_special_stack_items();
        $this->pkmn_stack = array_merge($this->pkmn_stack, $special_items);
    }
    
    /**
     * Returns the special items for the Pokémon stack.
     * Currently, just "egg" and "unknown". These have no shiny icons.
     */
    public function get_special_stack_items()
    {
        // Unhatched egg.
        $egg = array(
            'idx' => null,
            'slug' => array(
                'eng' => 'egg',
                'jpn' => 'tamago',
            ),
            'icons' => array(
                '.' => array(),
            ),
            'name' => array(
                'eng' => 'Egg',
                'jpn' => 'タマゴ',
                'jpn_ro' => 'Tamago',
            ),
        );
        
        // Unknown (not Unown) or glitch Pokémon.
        $unknown = array(
            'idx' => null,
            'slug' => array(
                'eng' => 'unknown',
                'jpn' => 'fumei',
            ),
            'icons' => array(
                '.' => array(),
            ),
            'name' => array(
                'eng' => 'Unknown',
                'jpn' => 'ふめい',
                'jpn_ro' => 'Fumei',
            ),
        );
        
        return array_merge(
            $this->get_pkmn_stack_items('egg', $egg, true),
            $this->get_pkmn_stack_items('unknown', $unknown, true)
        );
    }
    
    /**
     * Returns the icon stack (and generates it if it doesn't exist).
     *
     * @return mixed[] Pokémon icon stack data.
     */
    public function get_pkmn_icon_stack()
    {
        if (empty($this->pkmn_stack)) {
            $this->create_pkmn_icon_stack();
        }
        return $this->pkmn_stack;
    }
    
    /**
     * Returns the standard icons that need to be referred to
     * in the case of duplicates.
     *
     * @return mixed[] Pokémon icon stack data (only of standard icons).
     */
    public function get_pkmn_std_icons()
    {
        if (empty($this->pkmn_stack)) {
            $this->create_pkmn_icon_stack();
        }
        return $this->std_sprites;
    }
    
    /**
     * Produces and returns a variable name for an icon
     * based on its attributes.
     *
     * @param string $type Type identifier.
     * @param string $slug Slug name.
     * @param string $variation Variation.
     * @param string $subvariation Subvariation.
     * @param string $version Version.
     * @return string Variable name.
     */
    private function get_icon_var_name($type, $slug, $variation=null,
        $subvariation=null, $version=null)
    {
        $items = array();
        $items[] = $type;
        if ($type == 'pkmn') {
            $items[] = $slug[Settings::get('pkmn_language_slugs')];
            if ($variation != '.') {
                $items[] = $variation;
            }
            if ($subvariation != '.') {
                $items[] = $subvariation;
            }
            if ($version != 'regular') {
                $items[] = $version;
            }
        }
        else {
            $items[] = $slug;
        }
        return implode('-', $items);
    }
    
    
    /**
     * Returns stack items for a specific Pokémon.
     *
     * @return mixed[] Pokémon icon stack data.
     */
    private function get_pkmn_stack_items($id, $pkmn, $special=false)
    {
        // Base info that's the same for each stack item.
        $base_info = array(
            'id' => $id,
            'idx' => $pkmn['idx'],
            'type' => 'pkmn',
            'w' => $this->pkmn_img_width,
            'h' => $this->pkmn_img_height,
            'slug' => $pkmn['slug'][Settings::get('pkmn_language_slugs')],
            'slug_img' => $pkmn['slug'][Settings::get('img_slug_lang')],
            'section' => 'pkmn',
            'name_display' => $pkmn['name'][Settings::get('pkmn_language')],
        );
        
        // Retrieve some variables from the settings.
        $vars = array(
            'dir_base', 'dir_pkmn', 'dir_pkmn_female', 'dir_pkmn_right',
            'include_pkmn_forms', 'include_pkmn_right',
        );
        foreach ($vars as $var) {
            $$var = Settings::get($var);
        }
        
        // Keep this Pokémon's stack items and return them at the end.
        $tmp_stack = array();
        
        // Sort the variations to ensure the standard variation
        // comes first. The data file should already be pre-sorted,
        // but we're making sure.
        uksort($pkmn['icons'], array($this, 'pkmn_variation_sort'));
        
        foreach ($pkmn['icons'] as $icon => $icon_data) {
            // If this is a special icon (no variations, no shiny version),
            // add only the plain icon and continue.
            if ($special) {
                $var = $this->get_icon_var_name(
                    'pkmn',
                    $pkmn['slug'],
                    $icon,
                    '.',
                    'regular'
                );
                $pkmn_info = array_merge($base_info,
                    array(
                        'version' => 'regular',
                        'subvariation' => null,
                        'is_duplicate' => false,
                    ),
                    $this->get_pkmn_icon_fit(),
                    array(
                        'variation' => $icon,
                        'var' => $var,
                        'file' => (
                            $dir_base.
                            $dir_pkmn.
                            Settings::get('dir_pkmn_special').
                            $pkmn['slug'][Settings::get('img_slug_lang')].
                            '.png'
                        ),
                    )
                );
                $tmp_stack[] = $pkmn_info;
                continue;
            }
            
            // Loop through each icon twice: once for regular versions, and
            // (if requested), once more for shiny versions.
            // Every variation is added to the stack.
            
            foreach ($this->versions as $version) {
                // Refer to either the regular or shiny icon directory.
                $version_dir = Settings::get('dir_pkmn_'.$version);
                
                // Check to see if this variation doesn't actually
                // have its own icon, and simply needs to refer
                // to the default icon.
                $is_duplicate = (
                    !empty($icon_data['is_duplicate']) &&
                    $icon_data['is_duplicate']
                );
                
                $info = array(
                    'version' => $version,
                    'subvariation' => null,
                    'is_duplicate' => $is_duplicate,
                );
                // If this is a duplicate, keep a reference
                // to the standard icon.
                if ($is_duplicate) {
                    $info['original'] = @$this->std_sprites[$id][$version];
                }
                $is_standard = $icon == '.';
        
                // Don't include non-standard forms
                // if we've got that turned off.
                if ($include_pkmn_forms != true && !$is_standard) {
                    continue;
                }
        
                // The standard version is indicated by a single period.
                // All other variants are present as slug-variant.png.
                $variation = $is_standard ? '' : '-'.$icon;
                
                // Produce a variable name.
                $var = $this->get_icon_var_name(
                    'pkmn',
                    $pkmn['slug'],
                    $icon,
                    '.',
                    $version
                );
                
                // Add to the stack
                $pkmn_info = array_merge($base_info,
                    $info,
                    $this->get_pkmn_icon_fit(!$is_duplicate),
                    array(
                        'variation' => $icon,
                        'var' => $var,
                        'file' => (
                            $dir_base.
                            $dir_pkmn.
                            $version_dir.
                            $pkmn['slug'][Settings::get('img_slug_lang')].
                            $variation.'.png'
                        ),
                    )
                );
                $tmp_stack[] = $pkmn_info;
                
                // If this is the standard variant, save a copy by id.
                // We need to re-use it for variations that
                // don't have their own icon.
                if ($is_standard) {
                    $this->std_sprites[$id][$version] = $pkmn_info;
                }
                
                // Female variant
                if ($include_pkmn_forms && $icon_data['has_female']) {
                    $var = $this->get_icon_var_name(
                        'pkmn',
                        $pkmn['slug'],
                        $icon,
                        'female',
                        $version
                    );
                    $tmp_stack[] = array_merge($base_info,
                        $info,
                        $this->get_pkmn_icon_fit(!$is_duplicate),
                        array(
                            'variation' => $icon,
                            'var' => $var,
                            'subvariation' => 'female',
                            'file' => (
                                $dir_base.
                                $dir_pkmn.
                                $version_dir.
                                $dir_pkmn_female.
                                $pkmn['slug'][Settings::get('img_slug_lang')].
                                '.png'
                            ),
                        )
                    );
                }
                
                // Right-facing variant
                if ($include_pkmn_forms && $icon_data['has_right']
                &&  $include_pkmn_right > 0) {
                    $var = $this->get_icon_var_name(
                        'pkmn',
                        $pkmn['slug'],
                        $icon,
                        'right',
                        $version
                    );
                    $tmp_stack[] = array_merge($base_info,
                        $info,
                        $this->get_pkmn_icon_fit(!$is_duplicate),
                        array(
                            'variation' => $icon,
                            'var' => $var,
                            'subvariation' => 'right',
                            'file' => (
                                $dir_base.
                                $dir_pkmn.
                                $version_dir.
                                $dir_pkmn_right.
                                $pkmn['slug'][Settings::get('img_slug_lang')].
                                $variation.'.png'
                            ),
                        )
                    );
                }
                if ($include_pkmn_forms && !$icon_data['has_right']
                &&  $include_pkmn_right == 2) {
                    // If there's no right-facing variant, but we
                    // want all icons to have both left and right versions,
                    // we'll have to flip the regular image.
                    $var = $this->get_icon_var_name(
                        'pkmn',
                        $pkmn['slug'],
                        $icon,
                        'flipped',
                        $version
                    );
                    $tmp_stack[] = array_merge($base_info,
                        $info,
                        $this->get_pkmn_icon_fit(!$is_duplicate),
                        array(
                            'variation' => $icon,
                            'var' => $var,
                            'subvariation' => 'flipped',
                            'file' => (
                                $dir_base.
                                $dir_pkmn.
                                $version_dir.
                                $pkmn['slug'][Settings::get('img_slug_lang')].
                                $variation.'.png'
                            ),
                        )
                    );
                }
            }
        }
        
        return $tmp_stack;
    }
}