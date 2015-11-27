<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/25/2015
 * Time: 12:18 PM
 */

namespace SoftUni\Models;

include 'IdentityUser.php';

use SoftUni\Models;


class User extends Models\IdentityUser
{
    private $email;
    private $cash;

    public function getEmail() :string {
        return $this->email;
    }


}