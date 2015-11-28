<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Http;

class Session
{
    public function __construct(array $arguments = array())
    {
        if (!empty($arguments)) {
            $arguments = $_SESSION;
        }

        foreach ($arguments as $property => $argument) {
            $this->{$property} = $argument;
        }
    }

    public function __set($name, $value)
    {
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