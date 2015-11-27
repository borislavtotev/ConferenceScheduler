<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore;

class CommonFunction
{
    public static function getDirContents($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if(is_dir($path) && $value != "." && $value != "..") {
                CommonFunction::getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }
}