<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Annotations;

use SoftUni\FrameworkCore\Http\HttpContext;

class Annotation
{
    public function __construct()
    {
    }

    public function onInitialize(string $actionAnnotation, string $classAnnotation = null) :string
    {
        // be default return the name of the annotation
        return $actionAnnotation;
    }

    public static function isValid(string $property, HttpContext $httpContext) :bool
    {
    }
}