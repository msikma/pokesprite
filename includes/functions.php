<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Formats a number of bytes for human readability.
 *
 * @param int $bytes Amount of bytes to format.
 * @param int $precision Precision of the output.
 */
function format_bytes($bytes, $precision=2)
{
    // Taken from Stack Overflow <http://stackoverflow.com/a/2510459/2582271>.
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision).' '.$units[$pow];
}

/**
 * Generates a lowercase slug (URL-ready name) from a string.
 *
 * Designed to work only for Latin-based character sets.
 *
 * @param string $text Text to slugify.
 */
function slugify($text)
{
    // Taken from Stack Overflow <http://stackoverflow.com/a/2955878/2582271>.
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text;
}
