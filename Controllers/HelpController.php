<?php
declare(strict_types=1);

namespace SoftUni\Controllers;

use SoftUni\FrameworkCore\Annotations\AnnotationParser;

include_once('Controller.php');

class HelpController extends Controller
{
    public function routesMapping()
    {
        $annotations = AnnotationParser::$allAnnotations;

        echo json_encode($annotations);
    }


    /**
     * @Authorize
     * @param int $id
     */
    public function test(int $id)
    {
        echo "stana: $id";
    }
}