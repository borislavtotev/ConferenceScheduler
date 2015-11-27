<?php
declare(strict_types=1);

namespace SoftUni\Models;

include 'IdentityUser.php';

use SoftUni\Models;

class User extends Models\IdentityUser
{
    private $email;

    public function getEmail() :string
    {
        return $this->email;
    }
}