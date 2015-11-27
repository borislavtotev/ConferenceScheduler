<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Annotations;

include_once 'Annotation.php';

use \SoftUni\FrameworkCore\Annotations;
use \SoftUni\FrameworkCore\Database;
use \SoftUni\FrameworkCore\Http\HttpContext;

class AuthorizeAnnotation extends Annotations\Annotation
{
    public static function isValid(string $property, HttpContext $httpContext) :bool
    {
        $loggedUser = $httpContext->getLoggedUser();
        if ($loggedUser != null) {
            $loggedUserId = $httpContext->getLoggedUser()->getId();
            if ($loggedUserId != null) {
                if (preg_match("#Roles=['\"](.*?)['\"]#", $property, $match)) {
                    $roles = explode(",", strtolower($match[1]));
                    //var_dump($roles);
                    $dbRoles = Database::getUserRoles(1);
                    //var_dump($dbRoles);
                    foreach ($dbRoles as $dbRole) {
                        $del_val = strtolower($dbRole);
                        // remove from roles if the role is available in the db
                        if(($key = array_search($del_val, $roles)) !== false) {
                            unset($roles[$key]);
                        }
                    }

                    //var_dump($roles);
                    // if all roles are founded the $roles will be empty
                    if (count($roles) == 0) {
                        return true;
                    }

                    return false;
                } else if ($property == 'Authorize') {
                    return true;
                }

                return false;
            }
        }

        return false;
    }
}