<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/22/2015
 * Time: 1:43 PM
 */

namespace SoftUni\FrameworkCore\Annotations;


class Annotation
{
    public function __construct() {
    }

    public function onInitialize(string $actionAnnotation, string $classAnnotation = null) : string {
    }

    public static function onCall(array $annotation) {
    }
}