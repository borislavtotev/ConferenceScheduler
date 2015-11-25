<?php
namespace SoftUni\FrameworkCore;

use SoftUni\Models;

class Application
{
    private $controllerName;
    private $actionName;

    private $controller;

    private $dbContext;

    public function __construct(DatabaseContext $dbContext)
    {
        $this->dbContext = $dbContext;
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

    public function start()
    {
        $this->CheckAnnotations();
        $this->CheckUserConfiguration();
        Router::readAllRoutes();


        $uri = Router::make_uri();
        $params = Router::match_uri($uri);
        var_dump($params);
        if ($params)
        {
            $controller = ucwords($params['controller']);
            $this->actionName = $params['action'];

            unset($params['controller'], $params['action']);

            $this->controllerName = $controller;

            if (!class_exists($controller, true)) {
                $fullController = 'Softuni\\Controllers\\'.$controller;
                if (method_exists($fullController, $this->actionName)) {
                    $this->controller = new $fullController($this->dbContext);
                    View::$controllerName = $this->controllerName;
                    View::$actionName = $this->actionName;
                    call_user_func_array(array($this->controller, $this->actionName), $params);
                } else {
                    throw new \Exception("Method not found");
                }
            }
            else
            {
                throw new \Exception("Controller not found");
            }
        }
        else
        {
            throw new \Exception("Route not found");
        }
    }

    private function CheckAnnotations() {
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

    private function CheckUserConfiguration() {
        if (\SoftUni\Config\ApplicationRunConfig::UserConfig) {
            Database::createUserTable();
            Database::createRolesTable();
            Database::updateRolesTable();
            Database::createUserRolesTable();
        }
    }
}