<?php
declare(strict_types=1);

namespace SoftUni\Models\BindingModels;

include_once 'BindingModel.php';

class UserBindingModel extends BindingModel
{
    protected $username = '';
    protected $password = '';
    protected $confirm = '';

    public function __construct(string $username = null, string $password = null, string $confirm = null)
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setConfirm($confirm);
    }

    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }

    public function getConfirm()
    {
        return $this->confirm;
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