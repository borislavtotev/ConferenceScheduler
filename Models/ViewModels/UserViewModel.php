<?php
declare(strict_types=1);

namespace SoftUni\Models\ViewModels;

class UserViewModel
{
    private $id;
    private $username;
    private $password;

    public function __construct($username, $id = null)
    {
        $this->setId($id);
        $this->setUsername($username);
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
    private function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername() :string
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return $this
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword() :string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPassword(string $password)
    {
        $this->pass = $password;
        return $this;
    }
}