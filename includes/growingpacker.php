<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Growing Bin Packer algorithm for positioning icons in a spritesheet
 * using space as efficiently as possible.
 *
 * This code is a rewritten version of Jake Gordon's GrowingPacker algorithm.
 * See <http://codeincomplete.com/posts/2011/5/7/bin_packing/> for more info.
 * The algorithm has been adapted from Javascript to PHP and modified lightly.
 */
class GrowingPacker
{
    public $root;
    
    private $permit_growth_w;
    private $permit_growth_h;
    
    /**
     * Fits size sorted blocks using a binary tree packing algorithm.
     *
     * Note that $blocks needs to be converted to a StdObject temporarily
     * as arrays cannot be properly passed by reference. This algorithm
     * cannot work in PHP using arrays.
     *
     * For the best effects, $blocks needs to be sorted by max(width, height),
     * descending (with the largest elements appearing first).
     * The algorithm fails if a block is larger than the previous one.
     *
     * If no extra arguments are passed, the initial size is determined from
     * the first element, and growth is permitted in both directions.
     *
     * @param mixed[] $blocks Blocks to fit.
     * @param ?int $initial_w Initial width.
     * @param ?int $initial_h Initial height.
     * @param ?boolean $permit_growth_w Permits the stack to grow in width.
     * @param ?boolean $permit_growth_h Permits the stack to grow in height.
     */
    public function fit(&$blocks, $initial_w=null, $initial_h=null, $permit_growth_w=true, $permit_growth_h=true)
    {
        $len = count($blocks);
        $first_block = @reset($blocks);
        
        $this->permit_growth_w = $permit_growth_w;
        $this->permit_growth_h = $permit_growth_h;
        
        // Set the initial sizes.
        $w = isset($first_block) ? $first_block->w : 0;
        $h = isset($first_block) ? $first_block->h : 0;
        $w = isset($initial_w) ? $initial_w : $w;
        $h = isset($initial_h) ? $initial_h : $h;
        
        // Create the root object.
        $this->root = (object)array(
            'x' => 0,
            'y' => 0,
            'w' => $w,
            'h' => $h,
            'used' => false,
        );
        
        // Iterate over all our blocks and then either split or grow
        // nodes on the tree.
        foreach ($blocks as &$block) {
            $node = $this->find_node($this->root, $block->w, $block->h);
            
            if ($node) {
                $block->fit = $this->split_node($node, $block->w, $block->h);
            }
            else {
                $block->fit = $this->grow_node($block->w, $block->h);
            }
        }
    }
    
    private function find_node(&$root, $w, $h)
    {
        if (@$root->used) {
            $node = $this->find_node($root->right, $w, $h);
            if ($node) {
                return $node;
            }
            $node = $this->find_node($root->down, $w, $h);
            if ($node) {
                return $node;
            }
        }
        else
        if (($w <= $root->w) && ($h <= $root->h)) {
            return $root;
        }
        else {
            return null;
        }
    }
    
    private function split_node(&$node, $w, $h)
    {
        $node->used = true;
        $node->down = (object)array(
            'x' => $node->x,
            'y' => $node->y + $h,
            'w' => $node->w,
            'h' => $node->h - $h,
        );
        $node->right = (object)array(
            'x' => $node->x + $w,
            'y' => $node->y,
            'w' => $node->w - $w,
            'h' => $h,
        );
        return $node;
    }
    
    private function grow_node($w, $h)
    {
        $can_grow_down = ($w <= $this->root->w) && $this->permit_growth_h !== false;
        $can_grow_right = ($h <= $this->root->h) && $this->permit_growth_w !== false;
        
        $should_grow_right = $can_grow_right && ($this->root->h >= ($this->root->w + $w));
        $should_grow_down = $can_grow_down && ($this->root->w >= ($this->root->h + $h));
        
        if ($should_grow_right) {
            return $this->grow_right($w, $h);
        }
        else
        if ($should_grow_down) {
            return $this->grow_down($w, $h);
        }
        else
        if ($can_grow_right) {
            return $this->grow_right($w, $h);
        }
        else
        if ($can_grow_down) {
            return $this->grow_down($w, $h);
        }
        else {
            // Error: couldn't grow. This is likely because the input blocks
            // weren't sorted by size. They must be sorted by (preferably)
            // max(width, height), with the largest elements appearing first.
            return null;
        }
    }
    
    private function grow($args, $w, $h)
    {
        $this->root = $args;
        $node = $this->find_node($this->root, $w, $h);
        if ($node) {
            return $this->split_node($node, $w, $h);
        } else {
            return null;
        }
    }
    
    private function grow_right($w, $h)
    {
        $args = (object)array(
            'used' => true,
            'x' => 0,
            'y' => 0,
            'w' => $this->root->w + $w,
            'h' => $this->root->h,
            'down' => $this->root,
            'right' => (object)array(
                'x' => $this->root->w,
                'y' => 0,
                'w' => $w,
                'h' => $this->root->h,
            )
        );
        return $this->grow($args, $w, $h);
    }
    
    private function grow_down($w, $h)
    {
        $args = (object)array(
            'used' => true,
            'x' => 0,
            'y' => 0,
            'w' => $this->root->w,
            'h' => $this->root->h + $h,
            'down' => $this->root,
            'right' => (object)array(
                'x' => 0,
                'y' => $this->root->h,
                'w' => $this->root->w,
                'h' => $h,
            )
        );
        return $this->grow($args, $w, $h);
    }
}
