<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore;

use SoftUni\Config;

class UserRoles
{
    public static function getAllRoles()
    {
        if (self::checkUserRoleFile()) {
            return array_keys(Config\UserConfig::Roles);
        }

        throw new \Exception("Undefined user roles. Please define ROLES constant in Application Configuration.");
    }

    public static function getRoleNumber($roleName)
    {
        if (self::checkUserRoleFile()) {
                $roles = Config\UserConfig::Roles;
                return $roles[$roleName];
        }

        throw new \Exception("Undefined user roles. Please define ROLES constant in Application Configuration.");
    }

    private function checkUserRoleFile()
    {
        $filePath = 'Config'.DIRECTORY_SEPARATOR.'UserConfig.php';

        if (file_exists($filePath)) {
            require_once $filePath;

            if (!is_null(Config\UserConfig::Roles)) {
                return true;
            }
        }

        return false;
    }
}