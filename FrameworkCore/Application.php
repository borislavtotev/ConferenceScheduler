<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore;

use SoftUni\FrameworkCore\Http\HttpContext;
use SoftUni\Models;

class Application
{
    private $controllerName;
    private $actionName;

    private $controller;

    private $dbContext;
    private $httpContext;

    public function __construct(DatabaseContext $dbContext, HttpContext $httpContext)
    {
        $this->dbContext = $dbContext;
        $this->httpContext = $httpContext;
    }

    /**
    * @return DatabaseContext
    */
    public function getDbContext()
    {
        return $this->dbContext;
    }

    /**
     * @param DatabaseContext $dbContext
     * @return $this
     */
    public function setDbContext($dbContext)
    {
        $this->dbContext = $dbContext;
        return $this;
    }

    public function getHttpContext()
    {
        return $this->httpContext;
    }

    public function setHttpContext($httpContext)
    {
        $this->httpContext = $httpContext;
        return $this;
    }

    public function start()
    {
        $this->checkAnnotations();
        $this->checkUserConfiguration();
        Router::readAllRoutes();

        $uri = Router::make_uri();
        $allParams = Router::match_uri($uri);
        //var_dump($params);

        $isRouteFound = false;

        if (count($allParams)>0) {
            foreach ($allParams as $params) {
                //var_dump($params);
                foreach ($params as $param) {
                    if (count($param) > 0) {
                        //var_dump($param);
                        $controller = ucwords($param['controller']);
                        $this->actionName = $param['action'];

                        unset($param['controller'], $param['action']);

                        $this->controllerName = $controller;
                        //var_dump($controller);
                        //var_dump($this->actionName);
                        if (!class_exists($controller, true)) {
                            $fullController = 'Softuni\\Controllers\\' . $controller;
                            if (method_exists($fullController, $this->actionName)) {
                                $this->controller = new $fullController($this->dbContext, $this->httpContext);
                                View::$controllerName = $this->controllerName;
                                View::$actionName = $this->actionName;
                                $annotations = Annotations\AnnotationParser::$allAnnotations['byController'][$this->controllerName][$this->actionName];
                                //var_dump($annotations);
                                //var_dump($this->controllerName);
                                //var_dump($this->actionName);
                                $areValidAnnotations = $this->checkAnnotationsValidity($this->httpContext, $annotations);
                                //var_dump($annotations);
                                if ($areValidAnnotations) {
                                    //var_dump($this->httpContext->getRequest()->getType());
                                    if ($this->httpContext->getRequest()->getType() == 'POST') {
                                        //var_dump($this->actionName);
                                        // the binding model should be always the first element
                                        $parameter = new \ReflectionParameter([$fullController, $this->actionName], 0);
                                        $bindingClassName = $parameter->getClass()->name;
                                        try {
                                            $bindingModel = $this->createBindingModel($bindingClassName);
                                            $isRouteFound = true;
                                            call_user_func(array($this->controller, $this->actionName), $bindingModel);
                                        } catch (\Exception $e) {
                                            $_SESSION['error'] = $e->getMessage();
                                            header('Location: ' . $_SERVER['REQUEST_URI']);
                                        }
                                    } else {
                                        $isRouteFound = true;
                                        //var_dump($params);
                                        call_user_func_array(array($this->controller, $this->actionName), $params[0]['params'] ?? []);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$isRouteFound) {
            header("location: /errors/404");
            $_SESSION['error'] = "Route not found";
        }
    }

    private function checkAnnotations()
    {
        if (\SoftUni\Config\ApplicationRunConfig::CheckAnnotations) {
            \SoftUni\FrameworkCore\Annotations\AnnotationParser::getAnnotations();
            $myFile = fopen('Logs\annotations.txt', "w");
            $annotations = serialize(\SoftUni\FrameworkCore\Annotations\AnnotationParser::$allAnnotations);
            fwrite($myFile, $annotations);
            fclose($myFile);
        } else {
            $annotations = unserialize(file_get_contents('Logs\annotations.txt'));
            \SoftUni\FrameworkCore\Annotations\AnnotationParser::$allAnnotations = $annotations;
        }
    }

    private function checkUserConfiguration()
    {
        if (\SoftUni\Config\ApplicationRunConfig::UserConfig) {
            Database::updateRolesTable();
            Database::updateUserTable();
            Database::createUserRolesTable();
        }
    }

    private function checkAnnotationsValidity(HttpContext $httpContext, array $annotations = null) :bool
    {
        $valid = true;
        if ($annotations != null) {
            foreach ($annotations as $annotationType => $annotationProperty) {
                $annotationClassName = ucwords(strtolower($annotationType))."Annotation";
                //var_dump($annotationClassName);

                $annotationFullClassName = 'SoftUni\\FrameworkCore\\Annotations\\'.$annotationClassName;

                if (class_exists($annotationFullClassName)) {
                    $annotation = new $annotationFullClassName();
                    $validAnnotation = $annotation->isValid($annotationProperty, $httpContext);
                    //var_dump($validAnnotation);
                    if (!$validAnnotation) {
                         return false;
                    }
                }
            }
        }

        return $valid;
    }

    private function createBindingModel($bindingClassName)
    {
        $model = new $bindingClassName;
        $properties = Models\BindingModels\UserBindingModel::expose();
        foreach ($properties as $property => $value) {
            if (isset($_POST[$property])) {
                //var_dump($property);
                $setterName = 'set'.$property;
                $model->$setterName($_POST[$property]);
            } else {
                throw new \Exception("Can't build the binding model, because the $property is missing!");
            }
        }

        return $model;
    }
}