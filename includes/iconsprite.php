<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Generates an image file containing all sprites.
 */
class IconSprite
{
    /** @var mixed The sprite image object. */
    public $sprite;
    /** @var mixed[] Data on the two sections of the sprite image (pkmn, etc). */
    public $sections;
    /** @var mixed[] Copies of primary Pokémon positioning data for use with duplicates. */
    public $std_pkmn;
    /** @var boolean Whether to be verbose in giving feedback. */
    public $verbose;
    /** @var mixed[] Default sprites (base for a duplicate). */
    public $std_sprites = array();
    
    /**
     * Initializes the sprite.
     *
     * @param int $width Width of the sprite.
     * @param int $height Height of the sprite.
     * @param mixed[] $sections Image sprite section data.
     * @param boolean $verbose Verbosity in giving feedback.
     */
    function __construct($width, $height, $sections, $verbose=false)
    {
        $this->sprite = @imagecreatetruecolor($width, $height);
        imagesavealpha($this->sprite, true);
        $trnsp = @imagecolorallocatealpha($this->sprite, 0, 0, 0, 127);
        imagefill($this->sprite, 0, 0, $trnsp);
        
        $this->sections = $sections;
        $this->verbose = $verbose;
    }
    
    /**
     * Sets standard icons for use with duplicates.
     *
     * @param mixed[] $pkmn Standard icons info.
     */
    function set_pkmn_std_icons($pkmn)
    {
        $this->std_sprites = $pkmn;
    }
    
    /**
     * Adds a single icon to the stack.
     *
     * @param mixed[] $icon Icon (pkmn or etc group).
     */
    function add($icon)
    {
        $x = $icon['fit']['x'];
        $y = $icon['fit']['y'];
        
        // Put the other icons underneath the Pokémon icons.
        if ($icon['section'] != 'pkmn') {
            $y += $this->sections['pkmn'];
        }
        $w = $icon['w'];
        $h = $icon['h'];
        $file = $icon['file'];
        $slug = $icon['slug'];
        $type = $icon['type'];
        
        $indicator = ($type == 'pkmn' ? $icon['name_display'].' (variation='.$icon['variation'].', subvariation='.$icon['subvariation'].', version='.$icon['version'].')' : $slug);
        
        if ($icon['is_duplicate']) {
            // If this is a duplicate, we don't need to add it.
            return true;
        }
        
        if (!is_file($file)) {
            // If the file does not exist, skip this entry.
            if ($this->verbose) {
                print(I18n::lf('entry_skipped', array($file, $indicator)));
            }
            // Image not added.
            return false;
        }
        else {
            if ($this->verbose) {
                print(I18n::lf('entry_added', array($file, $indicator)));
            }
        }
        
        // Open the image data.
        $tmp = @imagecreatefrompng($file);
        
        // If we're adding a faux right-facing image, we have to manually
        // flip the image before copying it to the sprite.
        if (@$icon['subvariation'] == 'flipped') {
            $tmp = $this->x_imageflip($tmp, 'v');
        }
    
        // Copy the image to the sprite.
        @imagecopy($this->sprite, $tmp, $x, $y, 0, 0, $w, $h);
        @imagedestroy($tmp);
        
        // Image added.
        return true;
    }
    
    /**
     * Output the image.
     *
     * If $compress is false, only the lowest compression
     * ratio will be applied, as we'll be using pngcrush later.
     *
     * @param string $location Where to save the (temporary) file.
     * @param boolean $compress Whether to apply significant compression.
     */
    function output($location, $compress=true)
    {
        imagepng($this->sprite, $location, $compress === true ? 9 : 1);
    }
    
    /**
     * Flips an image horizontally, vertically or both.
     *
     * Used to generated flipped images of Pokémon icons from their
     * original icons. Note that, for some inexplicable reason, the position is
     * off by 1px normally. This function accounts for the difference. However,
     * exactly why this happens is unknown. If the PHP version is >= 5.5.0,
     * the native GD imageflip() function is used instead.
     *
     * This was adapted from the code provided on <http://php.net/manual/en/function.imagecopy.php#85992> by xafford.
     *
     * @param mixed $imgsrc Image source.
     * @param string $direction Direction in which to flip (h|v|b).
     *
     */
    function x_imageflip($imgsrc, $direction='b')
    {
        // If we've got a native function, use that.
        if (function_exists('imageflip')) {
            // This hasn't been verified to be accurate, though...
            imageflip($imgsrc,
                    $direction == 'h' ? IMG_FLIP_HORIZONTAL :
                 ($direction == 'v' ? IMG_FLIP_VERTICAL :
                 ($direction == 'b' ? IMG_FLIP_BOTH : -1))
            );
            return $imgsrc;
        }
        $width = imagesx($imgsrc);
        $height = imagesy($imgsrc);
    
        $src_x = 0;
        $src_y = 0;
        $src_width = $width;
        $src_height = $height;
    
        switch ($direction) {
        case 'h':
            $src_y = floor($height - 1);
            $src_height = floor(-$height - 1);
            break;
    
        case 'v':
            $src_x = floor($width - 1);
            $src_width = floor(-$width - 1);
            break;
    
        case 'b':
            $src_x = $width - 1;
            $src_y = $height - 1;
            $src_width = -$width - 1;
            $src_height = -$height - 1;
            break;
    
        default:
            return $imgsrc;
        }
    
        $imgdest = imagecreatetruecolor($width, $height);
        imagesavealpha($imgdest, true);
        $trnsp = @imagecolorallocatealpha($imgdest, 0, 0, 0, 127);
        imagefill($imgdest, 0, 0, $trnsp);
    
        if (imagecopyresampled($imgdest, $imgsrc, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height)) {
            return $imgdest;
        }
    
        return $imgsrc;
    }
    
    /**
     * Destroys the image object.
     */
    function destroy()
    {
        imagedestroy($this->sprite);
    }
}