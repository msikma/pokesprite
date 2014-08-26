<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Used to internationalize the application.
 */
class I18n
{
    /** @var mixed[] Language strings. */
    private static $lines = array();
    
    /** @var string Default language. */
    private static $default_lang = 'en_us';
    
    /** @var string Current language. */
    private static $curr_lang = '';
    
    /** @var mixed[] Output filters. */
    private static $filters = array();
    
    /** @var string[] Default language strings. */
    private static $DEFAULTS = array(
        // American English (en_us).
        'en_us' => array(
            'dex_prefix' => '#',
            'overview_id' => '#',
            'overview_dex' => 'Dex',
            'overview_name' => 'Name',
            'overview_icon' => 'Icon',
            'overview_html' => 'HTML',
            'overview_class' => 'Class',
            'overview_file' => 'File',
            'generated_on' => "Generated on %s.",
            'info' => "<frm=cyan::bold>%s %s %s</frm>\n<frm=purple>Generates optimized Pokémon SCSS sprite\n%s\n\n</frm>",
            'icon_dir_failure' => "<frm=red>Couldn't open icon directory (%s). Skipping.</frm>\n",
            'dir_create' => "Creating output directory.\n",
            'dir_error' => "<frm=red>Couldn't create directories.</frm>\n",
            'dir_success' => "<frm=green>Dirs successfully created.</frm>\n",
            'no_cli' => "<frm=red>We don't seem to be running via the command line. Keep in mind this tool was designed for command line usage, and hasn't been tested in other situations.</frm>\n",
            'pngcrush_missing' => "<frm=red>pngcrush seems to be missing (path: %s). By default, it needs to be in the tools directory, chmodded to be executable, and at least version 1.7.60.\nImage will not be optimized by pngcrush.</frm>\n",
            'no_images' => "<frm=red>Couldn't find the source images. Make sure they're in %sregular/ and %sshiny/.</frm>\n",
            'no_data' => "<frm=red>Couldn't find the Pokémon data file. Make sure it's named %s and is placed in the %s directory.</frm>\n",
            'sprite_stats' => "\nGenerating %dx%d sprite composed of %d images.\nMemory usage will be about %s.\n",
            'sprite_ready' => "\n<frm=green>Finished preparing the sprite image.</frm>\n",
            'sprite_saving' => "Saving sprite image to %s.\n",
            'icons_added' => "\nAdded %d images.\n",
            'icons_skipped' => "<frm=red>Failed to add %d images.</frm>\n",
            'icons_skipped_hint' => "<frm=red>Failed to add %d images. Use --verbose for details.</frm>\n",
            'pngcrush_start' => "Using pngcrush to optimize the image...\n",
            'pngcrush_success' => "<frm=green>Successfully optimized the image.\n  Old size: %s\n  New size: %s\n</frm>",
            'pngcrush_error' => "<frm=red>Couldn't optimize the image.</frm>\n",
            'sprite_del_old' => "Deleting the old image.\n",
            'scss_generating' => "Generating SCSS...\n",
            'js_generating' => "Generating JS...\n",
            'html_generating' => "\nGenerating HTML overview...\n",
            'markdown_generating' => "Generating Markdown overview...\n",
            'all_done' => "\n<frm=cyan::bold>All done!</frm>\n",
            'entry_skipped' => "  Couldn't find file %s for entry %s. Skipping.\n",
            'entry_added' => "<frm=green>  Added file </frm><frm=green::bold>%s</frm><frm=green> for entry </frm><frm=green::bold>%s</frm><frm=green>.</frm>\n",
            'arg_error_unknown' => 'unknown error',
            'arg_error_include_right' => '--include-right must be 0, 1 or 2, e.g. --include-right=1',
            'arg_error_icon_sets' => '--icon-sets must be a comma-separated list, e.g. --icon-sets=apricorn,pokeball',
            'arg_error_file_exts' => '--file-exts must be a comma-separated list, e.g. --file-exts=png,jpg',
            'arg_error_tpl' => "\n<frm=red>Error: %s</frm>\n",
            'tasks_overview' => "List of tasks to perform:\n",
            'task_pkmn' => "  Add Pokémon icons to sprite sheet (pkmn=%s, regular=%s, shiny=%s, forms=%s, right=%s, lang=%s)\n",
            'task_icon_sets' => "  Add item icons to sprite sheet\n",
            'task_optimize' => "  Optimize image\n",
            'task_html' => "  Generate HTML overview page\n",
            'task_markdown' => "  Generate Markdown overview page\n",
            'task_js' => "  Generate JS file with image coordinates\n",
            'task_scss' => "  Generate SCSS file with basic styling attributes\n",
        ),
        // Japanese (ja).
        'ja' => array(
            'dex_prefix' => '#',
            'overview_id' => '#',
            'overview_dex' => '図鑑',
            'overview_name' => '名前',
            'overview_icon' => 'アイコン',
            'overview_html' => 'HTML',
            'overview_class' => 'クラス',
            'overview_file' => 'ファイル',
            'generated_on' => "Generated on %s.",
            'info' => "<frm=cyan::bold>%s</frm>\n<frm=purple>Generates optimized Pokémon SCSS sprite\n%s\n\n</frm>",
            'icon_dir_failure' => "<frm=red>Couldn't open icon directory (%s). Skipping.</frm>\n",
            'dir_create' => "Creating output directory.\n",
            'dir_error' => "<frm=red>Couldn't create directories.</frm>\n",
            'dir_success' => "<frm=green>Dirs successfully created.</frm>\n",
            'no_cli' => "<frm=red>We don't seem to be running via the command line. Keep in mind this tool was designed for command line usage, and hasn't been tested in other situations.</frm>\n",
            'pngcrush_missing' => "<frm=red>pngcrush seems to be missing (path: %s). By default, it needs to be in the tools directory, chmodded to be executable, and at least version 1.7.60.\nImage will not be optimized by pngcrush.</frm>\n",
            'no_images' => "<frm=red>Couldn't find the source images. Make sure they're in %sregular/ and %sshiny/.</frm>\n",
            'no_data' => "<frm=red>Couldn't find the Pokémon data file. Make sure it's named %s and is placed in the %s directory.</frm>\n",
            'sprite_stats' => "\nGenerating %dx%d sprite composed of %d images.\nMemory usage will be about %s.\n",
            'sprite_ready' => "\n<frm=green>Finished preparing the sprite image.</frm>\n",
            'sprite_saving' => "Saving sprite image to %s.\n",
            'icons_added' => "\nAdded %d images.\n",
            'icons_skipped' => "<frm=red>Failed to add %d images.</frm>\n",
            'icons_skipped_hint' => "<frm=red>Failed to add %d images. Use --verbose for details.</frm>\n",
            'pngcrush_start' => "Using pngcrush to optimize the image...\n",
            'pngcrush_success' => "<frm=green>Successfully optimized the image.\n  Old size: %s\n  New size: %s\n</frm>",
            'pngcrush_error' => "<frm=red>Couldn't optimize the image.</frm>\n",
            'sprite_del_old' => "Deleting the old image.\n",
            'scss_generating' => "Generating SCSS...\n",
            'js_generating' => "Generating JS...\n",
            'html_generating' => "\nGenerating HTML overview...\n",
            'markdown_generating' => "\nGenerating Markdown overview...\n",
            'all_done' => "\n<frm=cyan::bold>All done!</frm>\n",
            'entry_skipped' => "  Couldn't find file %s for entry %s. Skipping.\n",
            'entry_added' => "<frm=green>  Added file </frm><frm=green::bold>%s</frm><frm=green> for entry </frm><frm=green::bold>%s</frm><frm=green>.</frm>\n",
            'arg_error_unknown' => 'unknown error',
            'arg_error_include_right' => '--include-right must be 0, 1 or 2, e.g. --include-right=1',
            'arg_error_icon_sets' => '--icon-sets must be a comma-separated list, e.g. --icon-sets=apricorn,pokeball',
            'arg_error_file_exts' => '--file-exts must be a comma-separated list, e.g. --file-exts=png,jpg',
            'arg_error_tpl' => "\n<frm=red>Error: %s</frm>\n",
            'tasks_overview' => "List of tasks to perform:\n",
            'task_pkmn' => "  Add Pokémon icons to sprite sheet (pkmn=%s, regular=%s, shiny=%s, forms=%s, right=%s, lang=%s)\n",
            'task_icon_sets' => "  Add item icons to sprite sheet\n",
            'task_optimize' => "  Optimize image\n",
            'task_html' => "  Generate HTML overview page\n",
            'task_markdown' => "  Generate Markdown overview page\n",
            'task_js' => "  Generate JS file with image coordinates\n",
            'task_scss' => "  Generate SCSS file with basic styling attributes\n",
        ),
    );
    
    /**
     * Populates the language array, optionally for a specific language.
     *
     * If no language is given, the default is used.
     *
     * @param mixed[] $lines Lines to merge into the language array.
     * @param string $lang Language to populate.
     */
    public static function set_lines($lines, $lang=null)
    {
        if (empty($lang)) {
            $lang = static::$curr_lang;
        }
        if (empty(static::$lines[$lang])) {
            static::$lines[$lang] = array();
        }
        static::$lines[$lang] = array_merge(static::$lines[$lang], $lines);
    }
    
    /**
     * Adds a filter to the i18n output.
     *
     * This allows you to automatically run formatting functions on the
     * output of the i18n output.
     *
     * @param mixed[] $ref Function reference.
     */
    public static function add_output_filter($ref)
    {
        static::$filters[] = $ref;
    }
    
    /**
     * Applies queued output filters to a string.
     *
     * @param string $str String to filter.
     */
    private static function apply_output_filters($str)
    {
        foreach (static::$filters as $filter) {
            $str = call_user_func($filter, $str);
        }
        return $str;
    }
    
    /**
     * Returns a formatted language string by its key, the
     * vsprintf replacement arguments, and optionally its language.
     *
     * @param string $line Line key to retrieve the value for.
     * @param mixed[] $args Replacement arguments (vsprintf()).
     * @param string $lang Language to retrieve the value for.
     */
    public static function lf($line, $args=array(), $lang=null)
    {
        if (empty($lang)) {
            $lang = static::$curr_lang;
        }
        $line_str = static::l($line, $lang);
        return vsprintf($line_str, $args);
    }
    
    /**
     * Returns a line from the language lines array by its key, and
     * optionally a language.
     *
     * @param string $line Line key to retrieve the value for.
     * @param string $lang Language to retrieve the value for.
     */
    public static function l($line, $lang=null)
    {
        if (empty($lang)) {
            $lang = static::$curr_lang;
        }
        return static::apply_output_filters(static::$lines[$lang][$line]);
    }
    
    /**
     * Sets the active program language.
     *
     * @param string $lang Language to set as the active one.
     */
    public static function set_language($lang)
    {
        static::$curr_lang = $lang;
    }
    
    /**
     * Merges the defaults into the language array.
     */
    public static function set_default_lines()
    {
        if (empty(static::$curr_lang)) {
            static::set_language(static::$default_lang);
        }
        foreach (static::$DEFAULTS as $lang => $lines) {
            static::set_lines($lines, $lang);
        }
    }
}
