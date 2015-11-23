<?php

namespace SoftUni\FrameworkCore;

use SoftUni\Config;

class Router
{
    private static $uri;
    private static $routes;

    public static function readAllRoutes()
    {
        self::$routes['DefaultConfig'] = Config\RouteConfig::DefaultFrameworkRouteConfigs;

        self::$routes['CustomConfig'] = Config\RouteConfig::CustomRouteConfigs;

        $routesFromAnnotations = Annotations\AnnotationParser::$allAnnotations['Routes'];
        self::$routes['Annotations'] = $routesFromAnnotations;
    }

    public static function make_uri()
    {
        if(!empty($_SERVER['PATH_INFO']))
        {
            self::$uri = $_SERVER['PATH_INFO'];
        }
        elseif (!empty($_SERVER['REQUEST_URI']))
        {
            self::$uri = $_SERVER['REQUEST_URI'];

            //removing index
            if (strpos(self::$uri, 'index.php') !== FALSE)
            {
                self::$uri = str_replace('index.php', '', self::$uri);
            }
        }

        return parse_url(trim(self::$uri, '/'), PHP_URL_PATH);
    }

    // returns params[] with controller, action, params
    public static function match_uri($uri)
    {
        if (empty(self::$routes))
        {
            throw new \Exception("Routes must not be empty", E_USER_ERROR);
        }

        $findRoute = self::checkAnnotationRoutes();

        if (is_null($findRoute)) {
            $findRoute = self::checkApplicationOrFrameworkRoutes("Application");
        }

        if (is_null($findRoute)) {
            $findRoute = self::checkApplicationOrFrameworkRoutes("Framework");
        }

        return $findRoute;
    }

    private function checkApplicationOrFrameworkRoutes($whatToCheck) {
        $applicationRoutes = self::$routes[$whatToCheck];
        $uriParams = [];

        If (isset($applicationRoutes)) {
            foreach ($applicationRoutes as $routePattern) {
                if (preg_match($routePattern, self::$uri, $match)) {
                    $uriParams['controller'] = $match['controller'];
                    $uriParams['action'] = $match['action'];
                    $uriParams['params'] = $match['params'];

                    return $uriParams;
                }
            }
        }

        return $uriParams;
    }

    private function checkAnnotationRoutes() {
        $annotationRoutes = self::$routes['Annotations'];
        $uriParams = [];
        //var_dump($annotationRoutes);
        foreach ($annotationRoutes as $route => $properties) {
            $controller = $properties[0];
            $action = $properties[1];

            // set proper regex for variables
            if (preg_match_all('#{(.*?):?(integer|string|double)}#', $route, $match)) {
                for ($i = 0; $i < count($match[0]); $i++) {
                    $parameter = $match[1][$i];
                    $variableType = $match[2][$i];
                    switch ($variableType) {
                        case "integer":
                            $regex = '(?<'.$parameter.'>\d+)';
                            break;
                        case "string":
                            $regex = '(?<'.$parameter.'>[A-Za-z]+)';
                            break;
                        case "double":
                            $regex=  '(?<'.$parameter.'>\d+(\.\d+)?)';
                            break;
                        default:
                            throw new \Exception("Invalid type of variable.");
                            break;
                    }

                    $route = str_replace($match[0][$i], $regex, $route);
                }
            }

            // check whether the current uri match the route
            if (preg_match('#'.$route.'#', self::$uri, $match)) {
                $uriParams['controller'] = $controller;
                $uriParams['action'] = $action;
                $keys = array_filter(array_keys($match), function ($key) use ($match) {
                    if (!is_integer($key)) {
                        return $key;
                    }
                });

                foreach ($keys as $key) {
                    $uriParams['params'][$key] = $match[$key];
                }

                //var_dump($uriParams);
                break;
            }
        }
        return $uriParams;
    }
}