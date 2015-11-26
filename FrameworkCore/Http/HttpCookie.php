<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/26/2015
 * Time: 3:28 PM
 */

namespace SoftUni\FrameworkCore\Http;


class HttpCookie
{
    public function __construct(array $arguments = array())
    {
        if (!empty($arguments)) {
            $arguments = $_COOKIE;
        }

        foreach ($arguments as $property => $argument) {
            $this->{$property} = $argument;
        }
    }

    public function __set($name, $value)
    {
        $_COOKIE[$name] = new stdClass();
        $_COOKIE[$name]->getValue = function () use ($value) {
            return $value;
        };

        $_COOKIE[$name]->delete = function () use ($name) {
            unset($_COOKIE[$name]);
        };
    }

    public function __get($name)
    {
        return $_COOKIE[$name]->getValue;
    }

    public function __isset($name)
    {
        return isset($_COOKIE[$name]);
    }

    public function __unset($name)
    {
        unset($_COOKIE[$name]);
    }
}