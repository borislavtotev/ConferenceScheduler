<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Http;

class Session
{
    public function __construct(array $arguments = array())
    {
        if (count($arguments) == 0) {
            $arguments = $_SESSION;
            //var_dump($_SESSION);
            //var_dump($arguments);
        }

//        var_dump(array_keys($arguments));
//        var_dump($_SESSION);
        //die;
        foreach ($arguments as $property => $argument) {
//           echo "arguments<br/>";
//            var_dump($property);
//            var_dump($argument);
            $this->{$property} = $argument;
        }
    }

    public function __set($name, $value)
    {
        if ($name == 'formToken' || $name == 'userId' || $name == 'username') {
            $_SESSION[$name] = $value;
            return;
        }

        $_SESSION[$name] =  new stdObject();

        $_SESSION[$name]->value = function () use ($value) {
            return $value;
        };

        $_SESSION[$name]->delete = function () use ($name) {
            unset($_SESSION[$name]);
        };
    }

    public function __get($name)
    {
        return $_SESSION[$name];
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    public function __call($method, $args) {
        return call_user_func_array(array($this->_instance, $method), $args);
    }
}

class stdObject {
    public function __call($method, $arguments) {
        return call_user_func_array(\Closure::bind($this->$method, $this, get_called_class()), $arguments);
    }
}