<?php
declare(strict_types=1);

namespace SoftUni\Models;

class IdentityUser
{
    private $id;
    private $username;
    private $password;

    public function __construct($username, $password, $id = null)
    {
        $this->setId($id)
            ->setUsername($username)
            ->setPassword($password);
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return $this
     */
    public function setUsername(string $username)
    {
        if (preg_match("#^[a-zA-Z\\d]*$#", $username)) {
            $this->username = $username;
        }
        else {
            throw new \Exception("The user can contain any letters or numbers, without spaces.");
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPassword(string $password)
    {
        if (strlen($password) >=4 ) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            throw new \Exception("The password should be at least 4 characters.");
        }

        return $this;
    }
}