<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Annotations;

include_once 'Annotation.php';

use SoftUni\FrameworkCore\Annotations;
use SoftUni\FrameworkCore\Http\HttpContext;

class RouteAnnotation extends Annotations\Annotation
{
    public function onInitialize(string $routeAnnotation, string $classRouteAnnotation = null) :string
    {
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


    public static function isValid(string $property, HttpContext $httpContext) :bool
    {
        //No additional checks are needed. The route annotation is checked by the router
        return true;
    }

    private static function createRouteRegex(string $fullRouteAnnotation) :string
    {
        if (preg_match_all('#{(.*?):?(int|float|string|bool)}#',  $fullRouteAnnotation, $match)) {
            for ($i = 0; $i < count($match[0]); $i++) {
                $parameter = $match[1][$i];
                $variableType = $match[2][$i];
                switch ($variableType) {
                    case "int":
                        $regex = '(?<' . $parameter . '>\d+?)';
                        break;
                    case "string":
                        $regex = '(?<' . $parameter . '>[^\/\\\]+?)';
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

                echo "$fullRouteAnnotation<br/>";
            }
        }

        return $fullRouteAnnotation;
    }
}