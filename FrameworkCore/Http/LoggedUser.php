<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/26/2015
 * Time: 3:33 PM
 */

namespace SoftUni\FrameworkCore\Http;


class LoggedUser
{
    private $username;
    private $id;

    public function __construct(int $id = null, string $username = null)
    {
        if ($id = null) {
            $this->setId($id);
        }

        if ($username = null) {
            $this->setUsername($username);
        }
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id = null)
    {
        $this->id = $id;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        return $this->username = $username;
    }
}