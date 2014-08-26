<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Used to display program usage information.
 *
 * Requires an external template file to determine what to display.
 */
class Usage extends TemplateFormatter
{
    /** @var string[] Arguments that require a value. */
    public static $ARG_REQ = array(
        'dir-icons:', 'dir-data:', 'file-data:',
        'dir-output:', 'file-output-img-tmp:', 'file-output-img:',
        'file-output-scss:', 'file-output-html:', 'file-output-md:',
        'tpl-scss:', 'tpl-html:', 'css-base-sel:', 'css-shiny-sel:',
        'css-female-sel:', 'css-right-sel:', 'css-inline-sel:',
        'css-block-sel:', 'path-pngcrush:', 'dir-pkmn:', 'dir-resources:',
        'pkmn-lang:', 'lang:',
        // Undocumented (debugging only):
        'pkmn-range:',
    );
    /** @var string[] Arguments that may have a value. */
    public static $ARG_OPT = array(
        'include-right::',
        'file-exts::',
        'icon-sets::',
    );
    /** @var string[] Arguments that can have no value. */
    public static $ARG_NONE = array(
        'exclude-pkmn', 'exclude-shiny', 'exclude-regular', 'exclude-forms',
        'exclude-icon-sets', 'verbose', 'monochrome', 'help', 'no-pngcrush',
        'generate-markdown',
        // Undocumented (debugging only):
        'html-no-slugs',
    );
    
    /** @var mixed[] Settings parsed from the command-line options. */
    public $opt_settings = array();
    
    /** @var boolean Whether the user should be shown the usage overview. */
    public $needs_usage = false;
    /** @var boolean Whether invalid or unknown arguments were passed. */
    public $argument_error = false;
    /** @var string|null Error identifier. */
    public $error_id = null;
    
    /**
     * Returns command-line arguments.
     *
     * @return mixed[] Command-line arguments.
     */
    private function get_opts()
    {
        // Only long-style options are supported.
        $long = array_merge(
            static::$ARG_REQ,
            static::$ARG_OPT,
            static::$ARG_NONE
        );
        $opts = getopt('', $long);
        return $opts;
    }
    
    /**
     * Analyzes the user's passed command-line arguments and makes
     * a determination of whether the user invoked the script correctly.
     */
    private function parse_opts()
    {
        // The user's arguments will create a settings file that can
        // be used to directly overwrite the current settings.
        $s = array();
        // Whether an argument error has occurred.
        $argument_error = false;
        // Error explanation identifier.
        $error_id = 'arg_error_unknown';
        
        $opts = $this->get_opts();
        
        // Iterate over the command-line arguments.
        foreach ($opts as $arg => $val) {
            if ($arg == 'help') {
                $this->needs_usage = true;
                break;
            }
            if ($arg == 'monochrome') {
                $s['debug_monochrome'] = true;
            }
            if ($arg == 'verbose') {
                $s['debug_img_inclusion'] = true;
            }
            if ($arg == 'exclude-icon-sets') {
                $s['include_icon_sets'] = false;
            }
            if ($arg == 'exclude-forms') {
                $s['include_pkmn_forms'] = false;
            }
            if ($arg == 'exclude-shiny') {
                $s['include_pkmn_shiny'] = false;
            }
            if ($arg == 'exclude-regular') {
                $s['include_pkmn_nonshiny'] = false;
            }
            if ($arg == 'exclude-pkmn') {
                $s['include_pkmn'] = false;
            }
            if ($arg == 'no-pngcrush') {
                $s['generate_optimized'] = false;
            }
            if ($arg == 'generate-markdown') {
                $s['generate_markdown'] = true;
            }
            if ($arg == 'html-no-slugs') {
                $s['html_no_slugs'] = true;
            }
            if ($arg == 'include-right') {
                $val_int = intval($val);
                if (!is_numeric($val) || $val_int < 0 || $val_int > 2) {
                    $argument_error = true;
                    $error_id = 'arg_error_include_right';
                    break;
                }
                $s['include_pkmn_right'] = $val_int;
            }
            if ($arg == 'icon-sets') {
                $val = trim(trim($val), ',');
                if ($val != '') {
                    $val = explode(',', $val);
                }
                if (empty($val)) {
                    $argument_error = true;
                    $error_id = 'arg_error_icon_sets';
                    break;
                }
                $s['etc_icon_sets'] = $val;
            }
            if ($arg == 'file-exts') {
                $val = explode(',', trim(trim($val), ','));
                if (empty($val)) {
                    $argument_error = true;
                    $error_id = 'arg_error_file_exts';
                    break;
                }
                $s['file_exts'] = $val;
            }
        }
        
        // These are settings values that are directly replaced by the values
        // of the command-line arguments.
        $var_replacements = array(
            'path-pngcrush' => 'pngcrush_path',
            'tpl-html' => 'html_tpl',
            'tpl-scss' => 'scss_tpl',
            'dir-resources' => 'resources_dir',
            'file-output-html' => 'html_output',
            'file-output-md' => 'md_output',
            'file-output-scss' => 'scss_output',
            'file-output-img' => 'img_output',
            'file-output-img-tmp' => 'img_output_tmp',
            'dir-output' => 'dir_output',
            'file-data' => 'file_pkmn_data',
            'dir-data' => 'dir_data',
            'dir-pkmn' => 'dir_pkmn',
            'dir-icons' => 'dir_base',
            'pkmn-range' => 'pkmn_range',
            'pkmn-lang' => 'pkmn_language',
            'lang' => 'i18n_language',
        );
        foreach ($opts as $arg => $val) {
            if (in_array($arg, array_keys($var_replacements))) {
                $var = $var_replacements[$arg];
                $s[$var] = $val;
            }
        }
        // Check for the language setting: set the slug language to the
        // Pokémon name language by default. However, if romanized Japanese
        // names are requested (jpn_ro), the slug language should be jpn.
        if (isset($s['pkmn_language'])) {
            $s['pkmn_language_slugs'] = $s['pkmn_language'];
        }
        if ($s['pkmn_language'] == 'jpn_ro') {
            $s['pkmn_language_slugs'] = 'jpn';
        }
        
        if ($argument_error == true) {
            $this->needs_usage = true;
        }
        
        $this->opt_settings = $s;
        $this->argument_error = $argument_error;
        $this->error_id = $error_id;
    }
    
    /**
     * Returns an array of user-defined settings to override the
     * current settings with, based on passed command-line arguments.
     *
     * @return mixed[] Settings array populated by command-line arguments.
     */
    public function get_user_settings()
    {
        if (empty($this->opt_settings)) {
            $this->parse_opts();
        }
        
        return $this->opt_settings;
    }
    
    /**
     * Trims the usage template prior to its decoration for proper display.
     */
    private function trim_usage_tpl()
    {
        $this->tpl = "\n".trim($this->tpl)."\n\n";
    }
    
    /**
     * Displays the program usage.
     */
    public function display_usage()
    {
        $this->trim_usage_tpl();
        
        // Retrieve the proper error string in case we've got an error.
        if ($this->argument_error) {
            $error_str = I18n::lf('arg_error_tpl',
                array(I18n::l($this->error_id))
            );
        }
        
        $usage_vars = array(
            'website' => Settings::get('website'),
            'revision' => Settings::get('revision'),
            'error' => $error_str,
            'copyright' => (
                Settings::get('copyright_str').
                "\n".Settings::get('copyright_gf')
            ),
        );
        
        // We'll replace variables and finally format the output
        // for use in a terminal.
        $trmfrm = new TerminalFormatter();
        $this->render($usage_vars);
        $usage = $this->get_buffer();
        $usage = $trmfrm->format($usage);
        print($usage);
    }
}
