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

class DeleteAnnotation extends Annotations\Annotation
{
    public static function isValid(string $property) :bool {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            return true;
        }

        return false;
    }
}