<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/26/2015
 * Time: 3:29 PM
 */

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
        $_SESSION[$name] = new stdClass();
        $_SESSION[$name]->getValue = function () use ($value) {
            return $value;
        };

        $_SESSION[$name]->delete = function () use ($name) {
            unset($_SESSION[$name]);
        };
    }

    public function __get($name)
    {
        return $_SESSION[$name]->getValue;
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }
}