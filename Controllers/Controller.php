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

    protected function addCSRF() {
        $token = md5(uniqid());
        $_SESSION['formToken'] = $token;
    }

    protected function checkCSRF() : bool {
        $postParams = $this->httpContext->getRequest()->getParameters();
        $token = $_SESSION['formToken'];

        if (!isset($postParams->formToken) || $postParams->formToken != $token) {
            return false;
        }

        return true;
    }
}