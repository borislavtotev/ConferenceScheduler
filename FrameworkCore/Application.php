<?php
namespace SoftUni\FrameworkCore;


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
        Router::readAllRoutes();

        $uri = Router::make_uri();
        $params = Router::match_uri($uri);
        //var_dump($params);
        if ($params)
        {
            $controller = ucwords($params['controller']);
            $this->actionName = $params['action'];

            unset($params['controller'], $params['action']);

            $this->controllerName = $controller;

            $classes = get_declared_classes();
            $pattern = '/.*\\\\'.$this->controllerName.'$/';
            //var_dump($pattern);
            $filteredClasses = array_filter($classes, function ($class) use ($pattern) {
                if (preg_match($pattern, $class, $match)) {
                    return $class;
                }
            });

            //var_dump($filteredClasses);

            if ($filteredClasses) {
                foreach ($filteredClasses as $filteredClass) {
                    //var_dump($filteredClass);
                    if (method_exists($filteredClass, $this->actionName)) {
                        $this->controller = new $filteredClass($this->dbContext);
                        View::$controllerName = $this->controllerName;
                        View::$actionName = $this->actionName;
                        call_user_func_array(array($this->controller, $this->actionName), $params);
                    } else {
                        throw new \Exception("Method not found");
                    }
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
}