<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

use SoftUni\FrameworkCore\DatabaseContext;

class Controller
{
    protected $dbContext;

    public function __construct(DatabaseContext $dbContext)
    {
        $this->dbContext = $dbContext;
    }

    public function isLogged()
    {
        return isset($_SESSION['id']);
    }

    public function createRoute()
    {

    }
}