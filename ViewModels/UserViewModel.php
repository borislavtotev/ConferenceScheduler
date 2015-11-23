<?php

namespace SoftUni\ViewModels;

class UserViewModel
{
    private $id;
    private $username;
    private $password;

    public function __construct($username, $password, $id = null)
    {
        $this->setId($id)
            ->setUsername($username)
            ->setPass($password);
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPass($password)
    {
        $this->pass = $password;
        return $this;
    }
}