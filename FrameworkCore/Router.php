<?php
declare(strict_types=1);

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

    private static function checkConfigRoutes($whatToCheck)
    {
        $routes = self::$routes[$whatToCheck];
        $allUriParams = [];

        if (isset($routes)) {
            foreach ($routes as $routePattern) {
                $uriParams = [];
                //var_dump($routePattern);
                //var_dump(self::$uri);

                // check whether the routePattern is defined as regex
                if (substr($routePattern, 0,1) == '#') {
                    if (preg_match_all($routePattern, self::$uri, $match)) {
                        //echo "machna<br/>";
                        //var_dump($routePattern);
                        $uriParams = self::getUriParams($match);
                        $allUriParams[] = $uriParams;
                    }
                // the route pattern is with the special syntax e.g. <controller:{string}>/<action:{int}>/<id:{int}>
                } else {
                    if (preg_match_all('(<(\w+)(:{(string|int|float|bool)})?>)', $routePattern, $matches, PREG_SET_ORDER)) {
                        $routePattern = str_replace('/', '\/', $routePattern);

                        //var_dump($matches);
                        foreach ($matches as $match) {
                            //var_dump($match);
                            $matchedGroup = $match[0]; // e.g. <controller:{string}>
                            $parameter = $match[1];
                            if (isset($match[3])) {
                                $varType = $match[3];
                            } else {
                                $varType = 'string';
                            }

                            switch ($varType) {
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

                            $routePattern = str_replace($matchedGroup, $regex, $routePattern);
                        }

                        $routePattern = '#^\/'.$routePattern.'$#i';

                        //var_dump($routePattern);

                        if (preg_match_all($routePattern, self::$uri, $match)) {
                            $uriParams = self::getUriParams($match);
                            $allUriParams[] = $uriParams;
                        }
                    }
                }
            }
        }

        return $allUriParams;
    }

    private static function checkAnnotationRoutes()
    {
        $annotationRoutes = self::$routes['Annotations'];
        $allUriParams = [];
        //echo "annotationRoutes:<br/>";
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

    private static function getUriParams(array $match) :array
    {
        //var_dump($match);

        $uriParams = [];
        $uriParams['controller'] = ucwords(strtolower($match['controller'][0]))."Controller";
        unset($match['controller']);
        $uriParams['action'] = $match['action'][0];
        unset($match['action']);

        // get custom route for all named group excl controller and action
        $keys = array_filter(array_keys($match), function ($key) use ($match) {
            if (!is_integer($key)) {
                return $key;
            }
        });

        foreach ($keys as $key) {
            $uriParams['params'][$key] = $match[$key][0];
        }

        //var_dump($uriParams);
        return $uriParams;
    }
}