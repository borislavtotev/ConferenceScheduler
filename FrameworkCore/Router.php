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

        $routesFromAnnotations = Annotations\AnnotationParser::$allAnnotations['byType']['Route'];
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

        $findRoute[] = self::checkAnnotationRoutes();
        $findRoute[] = self::checkConfigRoutes("CustomConfig");
        $findRoute[] = self::checkConfigRoutes("DefaultConfig");

        return $findRoute;
    }

    private static function checkConfigRoutes($whatToCheck) {
        $routes = self::$routes[$whatToCheck];
        $allUriParams = [];

        If (isset($routes)) {
            foreach ($routes as $routePattern) {
                $uriParams = [];
                //var_dump($routePattern);
                //var_dump(self::$uri);
                if (preg_match($routePattern, self::$uri, $match)) {
                    $uriParams['controller'] = ucwords(strtolower($match['controller']))."Controller";
                    $uriParams['action'] = $match['action'];
                    if (isset($match['params'])) {
                        $uriParams['params'] = $match['params'];
                    };

                    $allUriParams[] = $uriParams;
                }
            }
        }

        return $allUriParams;
    }

    private static function checkAnnotationRoutes() {
        $annotationRoutes = self::$routes['Annotations'];
        $allUriParams = [];
        //var_dump($annotationRoutes);
        foreach ($annotationRoutes as $route => $properties) {
            $uriParams = [];
            $controller = $properties['controller'];
            $action = $properties['action'];
            $route = $properties['property'];
            //echo "<br/>$route<br/>";

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
                $allUriParams[] = $uriParams;
            }
        }

        return $allUriParams;
    }
}