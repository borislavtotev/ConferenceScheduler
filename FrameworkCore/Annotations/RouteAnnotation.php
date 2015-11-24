<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/21/2015
 * Time: 11:47 PM
 */

namespace SoftUni\FrameworkCore\Annotations;

include 'Annotation.php';

use \SoftUni\FrameworkCore\Annotations;

class RouteAnnotation extends Annotations\Annotation
{
    public function onInitialize(string $routeAnnotation, string $classRouteAnnotation = null) : string {
        if (preg_match('/Route\((.*?)\)/', $routeAnnotation, $matches1)) {
            if ($classRouteAnnotation != null) {
                $fullRouteAnnotation = $classRouteAnnotation.'/'.$matches1[1];
            } else {
                $fullRouteAnnotation = $matches1[1];
            }

            $fullRouteAnnotation = str_replace('"', '', $fullRouteAnnotation);
            $fullRouteAnnotation = str_replace("'", "", $fullRouteAnnotation);
        } else {
            throw new \Exception("Invalid Action Route Annotation");
        }

        $fullRouteAnnotation = self::createRouteRegex($fullRouteAnnotation);

        return $fullRouteAnnotation;
    }


    public static function onCall(array $routeAnnotation) {
            $controller = $routeAnnotation['controller'];
            $action = $routeAnnotation['action'];
    }

    private static function createRouteRegex(string $fullRouteAnnotation) :string {
        if (preg_match_all('#{(.*?):?(int|float|string|bool)}#',  $fullRouteAnnotation, $match)) {
            for ($i = 0; $i < count($match[0]); $i++) {
                $parameter = $match[1][$i];
                $variableType = $match[2][$i];
                switch ($variableType) {
                    case "int":
                        $regex = '(?<' . $parameter . '>\d+)';
                        break;
                    case "string":
                        $regex = '(?<' . $parameter . '>[A-Za-z]+)';
                        break;
                    case "float":
                        $regex = '(?<' . $parameter . '>\d+(\.\d+)?)';
                        break;
                    case "bool":
                        $regex = '(?<' . $parameter . '>(false|true)';
                        break;
                    default:
                        throw new \Exception("Invalid type of variable.");
                        break;
                }

                $fullRouteAnnotation = str_replace($match[0][$i], $regex, $fullRouteAnnotation);

                //echo "$fullRouteAnnotation<br/>";
            }
        }

        return $fullRouteAnnotation;
    }
}