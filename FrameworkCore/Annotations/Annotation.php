<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/22/2015
 * Time: 1:43 PM
 */

namespace SoftUni\FrameworkCore\Annotations;


use SoftUni\FrameworkCore\Http\HttpContext;

class Annotation
{
    public function __construct() {
    }

    public function onInitialize(string $actionAnnotation, string $classAnnotation = null) : string {
        // be default return the name of the annotation
        return $actionAnnotation;
    }

    public static function isValid(string $property, HttpContext $httpContext) :bool {
    }
}