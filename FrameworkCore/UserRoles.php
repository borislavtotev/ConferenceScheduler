<?php

namespace SoftUni\FrameworkCore;

use SoftUni\Application\Config;

class UserRoles
{
    public static function getAllRoles() {
        if (self::checkUserRoleFile()) {
            return array_keys(unserialize(ROLES));
        }

        throw new \Exception("Undefined user roles. Please define ROLES constant in Application Configuration.");
    }

    public static function getRoleNumber($roleName) {
        if (self::checkUserRoleFile()) {
                $roles = unserialize(ROLES);
                return $roles[$roleName];
        }

        throw new \Exception("Undefined user roles. Please define ROLES constant in Application Configuration.");
    }

    private function checkUserRoleFile() {
        $filePath = 'Application'.DIRECTORY_SEPARATOR.'Config'
            .DIRECTORY_SEPARATOR.'UserRolesConfig.php';

        if (file_exists($filePath)) {
            require_once $filePath;

            if (!is_null(ROLES)) {
                return true;
            }
        }

        return false;
    }
}