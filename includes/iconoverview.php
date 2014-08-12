<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

require_once 'icontplfactory.php';

/**
 * Builds an HTML overview of all icons.
 */
class IconOverview extends IconTplFactory
{
    /** @var mixed[] A list of our icons. */
    public $icon_list;
    /** @var ?string The generated overview HTML. */
    public $overview;
    
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
     * Returns overview HTML code.
     *
     * @return string Overview HTML code.
     */
    public function get_overview()
    {
        if (empty($this->overview)) {
            $this->generate_overview();
        }
        return $this->overview;
    }
    
    /**
     * Generates overview HTML code.
     */
    public function generate_overview()
    {
        // Base icon selection classes.
        $cls_base = Settings::get('css_base_selector');
        $cls_shiny = 'color-shiny';
        $cls_female = 'gender-female';
        $cls_right = 'dir-right';
        
        // If we're only using shiny icons, omit the "color-shiny" class.
        $only_shiny = (
            Settings::get('include_pkmn_nonshiny') == false &&
            Settings::get('include_pkmn_shiny') == true
        );
        
        // Empty cells will get this content.
        $empty_cell = '–';
        
        ob_start();
?>
<table class="table pkspr-overview">
  <tr>
    <th class="n"><p><?= I18n::l('overview_id'); ?></p></th>
    <th class="idx"><p><?= I18n::l('overview_dex'); ?></p></th>
    <th class="name"><p><?= I18n::l('overview_name'); ?></p></th>
    <th class="icon"><p><?= I18n::l('overview_icon'); ?></p></th>
    <th class="example"><p><?= I18n::l('overview_html'); ?></p></th>
    <th class="file"><p><?= I18n::l('overview_file'); ?></p></th>
  </tr>
<?php
        $n = 0;
        // Iterate over all icons and their variants and add one to the list.
        foreach ($this->icon_list as $icon) {
            $idx = $icon['idx'];
            $name_display = $icon['name_display'];
            
            $idx_str = empty($idx) ? $empty_cell : sprintf(I18n::l('dex_prefix').'%03d', $idx);
            $name_str = empty($name_display) ? $empty_cell : $name_display;
            
            // Set up the icon CSS classes.
            $classes = array($cls_base);
            
            if ($icon['type'] == 'pkmn') {
                // For Pokémon sprite icons.
                $classes[] = $icon['type'].'-'.$icon['slug'];
                if ($icon['version'] != 'regular' && !$only_shiny) {
                    $classes[] = $cls_shiny;
                }
                if ($icon['variation'] != '.') {
                    $classes[] = 'form-'.$icon['variation'];
                }
                if ($icon['subvariation'] == 'female') {
                    $classes[] = $cls_female;
                }
                if ($icon['subvariation'] == 'right'
                ||  $icon['subvariation'] == 'flipped') {
                    $classes[] = $cls_right;
                }
            }
            else {
                // For all other icon types.
                $classes[] = $icon['set'].'-'.$icon['slug'];
            }
            $classes_str = implode(' ', $classes);
            $classes_str_safe = htmlspecialchars($classes_str);
            $html_id = 'icon_'.$n;
            
            $img_str = '<span id="'.$html_id.'" class="'.$classes_str.'"></span>';
            $decorator = '<script>PkSpr.decorate(\''.$html_id.'\');</script>';
            $example_str = '<pre><code>'.htmlspecialchars('<span class="').'<span class="class">'.$classes_str_safe.'</span>'.htmlspecialchars('"></span>').'</code></pre>';
            
            // Add <wbr /> (word break opportunity) tags to the file string.
            // This allows it to still be broken in case of lack of space.
            $file_str = str_replace('/', '/<wbr />', $icon['file']);
?>
  <tr>
    <td class="n"><p><?= $n; ?></p></td>
    <td class="idx"><p class="idx"><?= $idx_str; ?></p></td>
    <td class="name"><p><?= $name_str; ?></p></td>
    <td class="icon"><p><?= $img_str.$decorator; ?></p></td>
    <td class="example"><?= $example_str; ?></td>
    <td class="file"><p><?= $file_str; ?></p></td>
  </tr>
<?php
			$n += 1;
        }
?>
</table>
<?php
        $icons = ob_get_clean();
        
        // Decorate the template with our generated HTML.
        $html = $this->decorate_tpl_with_defaults($this->tpl, array(
            'icons' => $this->indent_lines($icons, 4),
            'resources_dir' => Settings::get('resources_dir'),
            'js_output' => Settings::get('js_output'),
            'script_date' => Settings::get('script_date'),
            'css_output' => str_replace('.scss', '.css', Settings::get('scss_output')),
            'title_str_html' => htmlspecialchars(Settings::get('title_str')),
            'script_date_html' => htmlspecialchars(Settings::get('script_date')),
            'copyright_website_html' => htmlspecialchars(Settings::get('copyright_website')),
        ));
        
        $this->overview = $this->process_output($html);
    }
}
