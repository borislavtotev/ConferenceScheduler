<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

use SoftUni\FrameworkCore\DatabaseContext;
use SoftUni\FrameworkCore\Http\HttpContext;

class Controller
{
    protected $dbContext;
    protected $httpContext;

    public function __construct(DatabaseContext $dbContext, HttpContext $httpContext)
    {
        $this->dbContext = $dbContext;
        $this->httpContext = $httpContext;
    }

    public function isLogged()
    {
        return isset($_SESSION['id']);
    }
}