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

    public static function getClassProperties(string $userClassName) :array
    {
        if (preg_match_all('#[\\\\]([^\\\\]*?)$#', $userClassName, $match)) {
            $className = $match[1][0];
        }

        $output = [];
        $handle = fopen('Models'.DIRECTORY_SEPARATOR.$className.'.php', "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(preg_match("#function get([^\\s\\(\\)]*)#", $line, $match)) {
                    $property = $match[1];
                    if (preg_match("#:\\s*(string|bool|float|int)#", $line, $matchReturnTypes)) {
                        $output[$property] = $matchReturnTypes[1];
                    } else if ($property == "Id") {
                        $output[$property] = 'int';
                    } else {
                        $output[$property] = 'string';
                    }
                }
            }

            fclose($handle);
        } else {
            throw new \Exception("Unable to find the class");
        }

        return $output;
    }

    public static function getUserProperties() :array
    {
        try {
            $userClassName = \SoftUni\Config\UserConfig::UserIdentityClassName;
            $identityUserProperties = \SoftUni\FrameworkCore\CommonFunction::getClassProperties('SoftUni\\Models\\IdentityUser');
            if ($userClassName != 'IdentityUser') {
                $customUserProperties = \SoftUni\FrameworkCore\CommonFunction::getClassProperties($userClassName);
                //var_dump($customUserProperties);
                $result = $identityUserProperties;
                foreach ($customUserProperties as $customUserProperty => $type) {
                    $result[$customUserProperty] = $type;
                }

                return $result;
            }

            return $identityUserProperties;
        }
        catch(\Exception $pe) {
            throw new \Exception('Could not get User Properties. ' . $pe->getMessage());
        }
    }
}