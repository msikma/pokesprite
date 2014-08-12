<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Container for program settings.
 */
class Settings {
    /** @var mixed[] Current settings. */
    public static $settings = array();
    
    /**
     * Loads settings from a file and merges its contents with the
     * current settings.
     *
     * @param string $filename Name of the settings file.
     */
    public static function load_settings_file($filename)
    {
        // Turn on output buffering to prevent accidental output.
        ob_start();
        include($filename);
        ob_get_clean();
        
        // We should now have an array $s with which to merge
        // the current settings.
        if (!empty($s)) {
            static::load_settings($s);
        }
    }
    
    /**
     * Merge the new settings into the old settings.
     *
     * @param mixed[] $new_settings New settings array.
     */
    public static function load_settings($new_settings)
    {
        // The old variables are maintained unless overwritten.
        static::$settings = array_merge(static::$settings, $new_settings);
    }
    
    /**
     * Returns a setting value by its key.
     *
     * @param string $key Settings key to return.
     * @return ?mixed Settings value.
     */
    public static function get($key)
    {
        return @static::$settings[$key];
    }
}
