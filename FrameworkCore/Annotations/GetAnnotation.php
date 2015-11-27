<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/26/2015
 * Time: 11:02 PM
 */

namespace SoftUni\FrameworkCore\Annotations;

include_once 'Annotation.php';

use \SoftUni\FrameworkCore\Annotations;
use \SoftUni\FrameworkCore\Http\HttpContext;

class GetAnnotation extends Annotations\Annotation
{
    public static function isValid(string $property, HttpContext $httpContext) :bool {
        $requestMethod = $httpContext->getRequest()->getType();
        if ($requestMethod == 'GET') {
            return true;
        }

        return false;
    }
}