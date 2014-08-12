<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

require_once 'icontplfactory.php';

/**
 * Generates the JS file for our sprite.
 */
class IconJS extends IconTplFactory
{
    /** @var mixed[] A tree structure of our icons. */
    public $icons;
    /** @var mixed[] A list icon set sizes. */
    public $icon_sizes;
    /** @var mixed[] Data on the two sections of the sprite image (pkmn, etc). */
    public $sections;
    /** @var ?string The generated JS. */
    public $js;
    
    /**
     * Sets the tree structure of the icon information and coordinates.
     *
     * @param mixed[] $tree Pokémon icon tree structure.
     */
    public function set_icon_tree($tree)
    {
        $this->icons = $tree;
    }
    
    /**
     * Sets the sizes of various icon sets.
     *
     * @param mixed[] $sizes Icon set size data.
     */
    public function set_icon_sizes($sizes)
    {
        $this->icon_sizes = $sizes;
    }
    
    /**
     * Sets the sprite sections.
     *
     * @param mixed[] $sections Sprite section data.
     */
    public function set_sprite_sections($sections)
    {
        $this->sections = $sections;
    }
    
    /**
     * Returns generated JS code.
     *
     * @return string JS code.
     */
    public function get_js()
    {
        if (empty($this->js)) {
            $this->generate_js();
        }
        return $this->js;
    }
    
    /**
     * Generates JS code corresponding to our Pokémon icons.
     */
    public function generate_js()
    {
        // Prevent warnings in case they're empty.
        if (empty($this->icons['pkmn'])) {
            $this->icons['pkmn'] = array();
        }
        if (empty($this->icons['etc'])) {
            $this->icons['etc'] = array();
        }
        
        // Determine the coordinates of each Pokémon icon.
        $coords = array();
        $right_exc = array();
        foreach ($this->icons['pkmn'] as $slug => $variations) {
            // Get one item to ascertain this Pokémon's index number.
            // It's three levels deep.
            $first = reset(reset(reset($variations)));
            $idx = sprintf('%03d', $first['idx']);
            
            foreach ($variations as $variation => $versions) {
                foreach ($versions as $version => $subvariations) {
                    foreach ($subvariations as $subvariation => $icon) {
                    	$type = $icon['type'];
                    	// When generating faux right-facing sprites, the
                    	// "flipped" subvariation should be renamed "right".
                    	// This is because it's identical to the JS.
                    	if ($subvariation == 'flipped') {
                    		$subvariation = 'right';
                    	}
                    	
                    	$data = $this->get_icon_display_data($icon);
                    	$coords[$type][$slug][$variation][$subvariation][$version] = $data;
                    }
                }
            }
        }
        // Determine the coordinates of other icons.
        foreach ($this->icons['etc'] as $type => $items) {
            foreach ($items as $slug => $item) {
                $data = $this->get_icon_display_data($item);
                $coords[$type][$slug] = $data;
            }
        }
        
        // Encode values as JSON.
        $json_opts = JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT;
        $sizes_json = json_encode($this->icon_sizes, $json_opts);
        $coords_json = json_encode($coords, $json_opts);
        
        // Decorate our template with the generated values.
        
        if (Settings::get('include_pkmn_nonshiny')) {
            $fallback_color = 'regular';
        }
        else {
            $fallback_color = 'shiny';
        }
        $js_output_min = str_replace(
            '.js',
            '.min.js',
            Settings::get('js_output')
        );
        $js = $this->decorate_tpl_with_defaults($this->tpl, array(
            'sizes_json' => $sizes_json,
            'coords_json' => $coords_json,
            'js_output_min' => $js_output_min,
            'fallback_color' => $fallback_color,
            'var_base_name' => Settings::get('var_base_name'),
        ));
        
        $this->js = $this->process_output($js);
    }
    
    /**
     * Retrieves size and coordinate information for an icon.
     *
     * In case the icon's type has a defined set size, the width
     * and height will not be returned.
     *
     * @param mixed[] $icon Icon data.
     * @return int[] Icon coordinates and optionally width.
     */
    public function get_icon_display_data($icon)
    {
        // If this icon refers to an original (i.e. it's a form that is
        // identical to the standard icon), refer to the original's data.
        if (@$icon['original']) {
            $x = $icon['original']['fit']['x'];
            $y = $icon['original']['fit']['y'];
            $w = $icon['original']['w'];
            $h = $icon['original']['h'];
        }
        else {
            $x = $icon['x'];
            $y = $icon['y'];
            $w = $icon['w'];
            $h = $icon['h'];
        }
        // At least return x and y coordinates.
        $data = array(
            'x' => intval($x),
            'y' => intval($y),
        );
        // If this type isn't part of a set with a set size, add the size.
        $type = $icon['type'] == 'etc' ? $icon['set'] : $icon['type'];
        if (!isset($this->icon_sizes[$type]['w'])) {
            $data['w'] = intval($w);
            $data['h'] = intval($h);
        }
        return $data;
    }
}
