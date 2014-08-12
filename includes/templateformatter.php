<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Used to replace variables in template files.
 */
class TemplateFormatter
{
    /** @var string Current usage template. */
    public $tpl = '';
    
    /** @var string Rendered template buffer. */
    public $buffer = '';
    
    /** @var mixed[] Template replacement variables. */
    public $tpl_vars = array();
    
    /**
     * Loads a usage template from a file.
     *
     * @param string $filename Name of the template file.
     */
    public function load_tpl_file($filename)
    {
        $contents = @file_get_contents($filename);
        if (!empty($contents)) {
            $this->tpl = $contents;
        }
    }
    
    /**
     * Merges new variables into the template replacement variables array.
     *
     * @param mixed[] $vars Array of new template replacement variables.
     */
    public function register_vars($vars)
    {
        $this->tpl_vars = array_merge($this->tpl_vars, $vars);
    }
    
    /**
     * Renders the template by replacing variables and storing the result
     * in the buffer.
     *
     * Optionally, one can pass an array of template replacement variables.
     *
     * @param mixed[] $vars Array of new template replacement variables.
     */
    public function render($vars=array())
    {
        if (!empty($vars)) {
            $this->register_vars($vars);
        }
        $this->buffer = $this->var_repl($this->tpl, $this->tpl_vars);
    }
    
    /**
     * Replaces template variables with their values, as taken
     * from the $tpl_vars array.
     *
     * @param string $tpl Template to decorate.
     * @param mixed[] $vars Array of replacement variables.
     */
    private function var_repl($tpl, $vars)
    {
        foreach ($vars as $k => $v) {
            $tpl = str_replace('{{$'.$k.'}}', $v, $tpl);
        }
        return $tpl;
    }
    
    /**
     * Returns the rendered template file.
     *
     * @return string The current output buffer.
     */
    public function get_buffer()
    {
        return $this->buffer;
    }
    
    /**
     * Outputs the rendered template file.
     */
    public function output_buffer($str)
    {
        print($this->buffer);
    }
}
