<?php
declare(strict_types=1);

namespace SoftUni\Models\BindingModels;

include_once 'BindingModel.php';

class UserLoginBindingModel extends BindingModel
{
    protected $username = '';
    protected $password = '';

    public function __construct(string $username = null, string $password = null)
    {
        $this->setUsername($username);
        $this->setPassword($password);
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
}