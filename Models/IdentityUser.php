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
        $this->setUsername($username)
            ->setPassword($password);

        if ($id != null) {
            $this->setId($id);
        }
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
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername() : string
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
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}