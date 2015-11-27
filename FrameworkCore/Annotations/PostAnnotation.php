<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Annotations;

include_once 'Annotation.php';

use \SoftUni\FrameworkCore\Annotations;
use \SoftUni\FrameworkCore\Http\HttpContext;

class PostAnnotation extends Annotations\Annotation
{
    public static function isValid(string $property, HttpContext $httpContext) :bool
    {
        $requestMethod = $httpContext->getRequest()->getType();
        if ($requestMethod == 'POST') {
            return true;
        }

        return false;
    }
}