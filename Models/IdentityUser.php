<?php
declare(strict_types=1);

namespace SoftUni\Models;

class IdentityUser
{
    private $id;
    private $username;
    private $password;

    public function __construct(string $username, string $password, int $id = null)
    {
        $this->setUsername($username);
        $this->setPassword($password);

        if ($id != null) {
            $this->setId($id);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        if (!isset($username)) {
            throw new \Exception("The username can't be empty");
        }

        if (preg_match("#^[a-zA-Z\\d]*$#", $username)) {
            $this->username = $username;
        }
        else {
            throw new \Exception("The user can contain any letters or numbers, without spaces.");
        }

        return $this;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}