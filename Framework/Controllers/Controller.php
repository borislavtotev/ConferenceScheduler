<?php
namespace SoftUni\Controllers;

abstract class Controller
{
    public function isLogged()
    {
        return isset($_SESSION['id']);
    }

    public function createRoute()
    {

    }
}