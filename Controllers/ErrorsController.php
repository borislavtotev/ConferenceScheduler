<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

use SoftUni\FrameworkCore\View;

include_once('Controller.php');

class ErrorsController extends Controller
{
    /**
     * @Route('errors/404');
     */
    public function notFound() {
        return new View('error', null);
    }
}