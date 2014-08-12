<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

require_once 'icontplfactory.php';

/**
 * Generates the SCSS file for our sprite.
 */
class IconStyler extends IconTplFactory
{
    /** @var mixed[] A list of our icons. */
    public $icon_list;
    /** @var mixed[] A list icon set sizes. */
    public $icon_sizes;
    /** @var ?string The generated SCSS. */
    public $scss;
    
    /**
     * Sets the tree structure of the icon information and coordinates.
     *
     * @param mixed[] $tree Pokémon icon tree structure.
     */
    public function set_icon_list($tree)
    {
        $this->icon_list = $tree;
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
     * Returns generated SCSS code.
     *
     * @return string SCSS code.
     */
    public function get_scss()
    {
        if (empty($this->scss)) {
            $this->generate_scss();
        }
        return $this->scss;
    }
    
    /**
     * Generates SCSS code corresponding to our Pokémon icons.
     */
    public function generate_scss()
    {
        $var_base_name = Settings::get('var_base_name');
        
        // Generate the variables.
        ob_start();
?>
$<?= $var_base_name; ?>-types: (
<?php
        foreach ($this->icon_sizes as $var => $size) {
            // Check whether this icon type has variable sizes or not.
            if (isset($size['w']) && isset($size['h'])) {
?>
  <?= $var; ?>: (
    w: <?= $size['w']; ?>, h: <?= $size['h']; ?>,
  ),
<?php
            }
            else {
?>
  <?= $var; ?>: (),
<?php
            }
        }
?>
);
$<?= $var_base_name; ?>-coords: (
<?php
        // Go over each icon and save its coordinates and size.
        foreach ($this->icon_list as $pos) {
            // If this icon is a duplicate,
            // refer to the original icon's coordinates.
            if (isset($pos['original'])) {
            	$x = $pos['original']['fit']['x'];
            	$y = $pos['original']['fit']['y'];
            }
            else {
                $x = $pos['fit']['x'];
                $y = $pos['fit']['y'];
            }
            // We'll add the width and height variables to each item
            // for completeness purposes, even though in practice the
            // size will be dictated by the item type.
            // (Except in the case of icon collections containing
            // images of multiple sizes.)
            $attrs = array(
                'x' => intval($x),
                'y' => intval($y),
                'w' => intval($pos['w']),
                'h' => intval($pos['h']),
            );
            $attr_list = array();
            foreach ($attrs as $k => $v) {
                $attr_list[] = $k.': '.$v;
            }
?>
  <?= $pos['var']; ?>: (<?= implode(', ', $attr_list); ?>),
<?php
        }
?>
);
<?php
        $item_vars = ob_get_clean();
        
        // Go over each icon type and save its size.
        ob_start();
        foreach ($this->icon_sizes as $var => $size) {
?>
&[class*='<?= $var; ?>-'] {
  @include <?= $var_base_name; ?>-type('<?= $var; ?>');
}
<?php
        }
        $type_sizes = ob_get_clean();
        
        // Add all our generated code to the tempalte file.
        $scss = $this->decorate_tpl_with_defaults($this->tpl, array(
            'item_vars' => $item_vars,
            'type_sizes' => $this->indent_lines($type_sizes, 2),
            'var_base_name' => $var_base_name,
            'img_output' => Settings::get('img_output'),
        ));
        
        $this->scss = $this->process_output($scss);
    }
}
