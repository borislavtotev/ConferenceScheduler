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
    public function __construct() {
    }

    public function onInitialize(string $routeAnnotation, string $classRouteAnnotation = null) : string {
        if ($classRouteAnnotation != null) {
            if (preg_match('/Route\((.*?)\)/', $classRouteAnnotation, $matches)) {
                $classRouteAnnotation = $matches[1];
            } else {
                throw new \Exception("Invalid Class Route Annotation");
            }
        }

        if (preg_match('/Route\((.*?)\)/', $routeAnnotation, $matches1)) {
            $fullRouteAnnotation = $classRouteAnnotation.'/'.$matches1[1];
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
        if (preg_match_all('#{(.*?):?(integer|string|double)}#',  $fullRouteAnnotation, $match)) {
            echo json_encode($match, JSON_PRETTY_PRINT)."<br/>";
            for ($i = 0; $i < count($match[0]); $i++) {
                $parameter = $match[1][$i];
                $variableType = $match[2][$i];
                switch ($variableType) {
                    case "integer":
                        $regex = '(?<' . $parameter . '>\d+)';
                        echo print_r($regex)."<br/>";
                        break;
                    case "string":
                        $regex = '(?<' . $parameter . '>[A-Za-z]+)';
                        break;
                    case "double":
                        $regex = '(?<' . $parameter . '>\d+(\.\d+)?)';
                        break;
                    default:
                        throw new \Exception("Invalid type of variable.");
                        break;
                }

                echo "$parameter<br/>";

                $fullRouteAnnotation = str_replace($match[0][$i], $regex, $fullRouteAnnotation);
            }
        }

        return $fullRouteAnnotation;
    }
}