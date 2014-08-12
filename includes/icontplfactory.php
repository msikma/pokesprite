<?php

// PokéSprite
// ----------
// The use of this source code is governed by the MIT license.
// See the COPYRIGHT file for more information.

namespace PkSpr;

/**
 * Base class for loading in templates and replacing placeholder variables.
 */
class IconTplFactory
{
    /** @var string Contents of the template file. */
    public $tpl;
    /** @var string Path to the template file. */
    public $tpl_file;
    /** @var mixed[] Variables used to render the output. */
    public $tpl_vars;
    
    /**
     * Loads the template file into a buffer.
     */
    public function register_tpl($file)
    {
        $this->tpl_file = $file;
        $this->tpl = file_get_contents($file);
    }
    
    /**
     * Saves an array of variables, to be used later when
     * rendering the output.
     */
    public function register_vars($vars)
    {
        $this->tpl_vars = $vars;
    }
    
    /**
     * Replace placeholders in a template with the provided values.
     *
     * For example, in a template containing {{$my_key}}, passing
     * array('my_key' => 'hi') will replace the key with 'hi'.
     *
     * @param string $tpl Template string to process.
     * @param mixed[] $args Arguments to use for replacement.
     * @return string Decorated template.
     */
    public function decorate_tpl($tpl, $args)
    {
        foreach ($args as $k => $v) {
            $tpl = str_replace('{{$'.$k.'}}', $v, $tpl);
        }
        return $tpl;
    }
    
    /**
     * Performs the final modifications to the output.
     *
     * @param string $str String to process.
     * @param boolean $write_bom Whether to write a BOM to the file.
     * @return string Processed output.
     */
    public function process_output($str, $write_bom=false)
    {
        $str = $this->normalize_lines($str);
        
        // Write the BOM. We assume the input is UTF-8.
        if ($write_bom) {
            $bom = chr(239).chr(187).chr(191);
            $str = $bom.$str;
        }
        
        return $str;
    }
    
    /**
     * Calls the decorate_tpl() function with a template name and
     * an array of variables.
     *
     * Prior to performing the decoration, the passed variables
     * are merged with the default variables.
     *
     * @param string $tpl Template string to process.
     * @param mixed[] $args Arguments to use for replacement.
     * @return string Decorated template.
     */
    public function decorate_tpl_with_defaults($tpl, $args)
    {
        $args = array_merge($this->tpl_vars, $args);
        return $this->decorate_tpl($tpl, $args);
    }
    
    /**
     * Indents lines using either tabs or spaces.
     *
     * By default, spaces are used.
     *
     * @param string $str String to indent.
     * @param int $level Level of indentation.
     * @param string $char Character to use for indentation.
     * @return string Indented string.
     */
    public function indent_lines($str, $level=2, $char=' ')
    {
        $lines = preg_split('/\n|\r/', $str, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($lines as &$line) {
            $line = str_repeat($char, $level).$line;
        }
        return implode("\n", $lines);
    }
    
    /**
     * Replaces all linebreaks with cross-platform \r\n sequences.
     *
     * @param string $str Lines to normalize.
     * @return string Normalized lines.
     */
    private function normalize_lines($str)
    {
        $str = preg_replace('~\R~u', "\r\n", $str);
        return $str;
    }
}
