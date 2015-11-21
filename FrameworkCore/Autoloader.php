<?php
namespace SoftUni;

use SoftUni\Core\Annotations;

class Autoloader
{
    public static function init()
    {
        spl_autoload_register(function($class) {
            // remove vendor from the class path
            $position = stripos($class, "\\");
            $class = substr($class, $position);
            $class = str_replace('\\', '\\\\', $class);

            // find proper file for this class
            $dirs = Annotations::getDirContents($_SERVER['DOCUMENT_ROOT']);
            $classFile = array_filter($dirs, function($dir) use($class) {
                $pattern = '/'.$class.'/';
                if (preg_match($pattern, $dir)) {
                    return $dir;
                }
            });

            // require the file if it is found
            $firstMatchedFile = array_pop($classFile);
            if (isset($firstMatchedFile)) {
                require_once $firstMatchedFile;
            }
        });
    }
}