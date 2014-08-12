<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Simple formatter for data display in a terminal.
 *
 * See the TerminalFormatter->format() function for an explanation
 * of the markup system.
 */
class TerminalFormatter
{
    /**
     * Adds ISO 6429 terminal color escape sequences to a string.
     *
     * There are eight defined colors: white, red, green, yellow, blue, purple,
     * cyan, and white. Aside from the colors, the text can be set to
     * three effects: bold, underline, blink.
     *
     * @param string $str String to be enclosed in the escape sequence.
     * @param string $fg Foreground color (default: white).
     * @param string $bg Background color (default: null).
     * @param string[] $effects Array of effects to use (default: null).
     */
    public function term_color($str, $fg='white', $bg=null, $effects=array())
    {
        // Terminal colors and effects as defined by the ISO 6429 standard.
        // To get a foreground color, add 30; for a background color, add 40.
        $iso_colors = array(
            'black' => 0,
            'red' => 1,
            'green' => 2,
            'yellow' => 3,
            'blue' => 4,
            'purple' => 5,
            'cyan' => 6,
            'white' => 7,
        );
        $iso_effects = array(
            'bold' => 1,
            'underline' => 4,
            'blink' => 5,
        );
    
        // Iterate over the user's desired effects and put the numbers in a list.
        $parsed_effects = array();
        if (!empty($effects)) {
            foreach ($effects as $effect) {
                $parsed_effects[] = $iso_effects[$effect];
            }
        }
    
        // Check if our foreground and background colors are sane.
        if (!in_array($fg, array_keys($iso_colors))) {
            $fg = 'white';
        }
        if (!in_array($bg, array_keys($iso_colors))) {
            $bg = null;
        }
        // Turn the color strings into ISO color numbers.
        // The background color can be blank, but the foreground color must be set.
        $fg_color = 30 + $iso_colors[$fg];
        $bg_color = $bg == null ? '' : 40 + $iso_colors[$bg].';';
    
        // Prepare and return the formatted string.
        $color_str = $bg_color.$fg_color;
        $effects_str = implode(';', $parsed_effects);
    
        return vsprintf("\033[%s;%sm%s\033[0m", array(
            $effects_str,
            $color_str,
            $str,
        ));
    }
    
    /**
     * Formats a string for display in a terminal in accordance with our
     * internal formatting schema.
     *
     * Text can be marked up using the following schema:
     * <frm=[foreground color][:background color][:list of effects]>text</frm>.
     * The foreground and background colors may be one of the ISO 6429
     * terminal colors: white, red, green, yellow, blue, purple, cyan,
     * and black. If a background color is defined, the foreground color
     * must be defined too. The list of effects must be comma-separated and
     * may include bold, underline and blink.
     *
     * Some examples follow.
     * 
     * <frm=red:yellow>Red text with yellow background</frm>
     * <frm=red::bold>Bold red text</frm>
     * <frm=blue:yellow:bold,underline>Blue/yellow, bold and underlined</frm>
     * <frm=::blink>Blinking text (default color)</frm>
     *
     * Nesting of these tags is not possible. So to follow red text
     * up with red text with a yellow background, do the following:
     *
     * This is <frm=red>red text, and now </frm><frm=red:yellow>with a
     * background too</frm>.
     * 
     * @param string $str The string to format.
     */
    public function format($str)
    {
        $str = preg_replace_callback(
            '/<frm=(.+?)>(.+?)<\/frm>/is',
            array(self, 'cb_format_style'),
            $str
        );
        return $str;
    }
    
    /**
     * Styles a regular expression string match. (Callback.)
     *
     * @param string[] $matches Regular expression matches.
     */
    private function cb_format_style($matches)
    {
        $style = self::parse_format_arg($matches[1]);
        return self::term_color(
            $matches[2],
            $style['fg'],
            $style['bg'],
            $style['effects']
        );
    }
    
    /**
     * Parses styling values extracted out of the <frm> tag.
     *
     * @param string $arg Argument taken from the <frm> tag.
     */
    private function parse_format_arg($arg)
    {
        $bits = explode(':', $arg);
        $fg = @empty($bits[0]) ? null : $bits[0];
        $bg = @empty($bits[1]) ? null : $bits[1];
        $effects = empty($bits[2]) ? null : @explode(',', $bits[2]);
        return array('fg' => $fg, 'bg' => $bg, 'effects' => $effects);
    }
}
