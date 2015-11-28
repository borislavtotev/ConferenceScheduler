<?php
declare(strict_types=1);

namespace SoftUni\Models\BindingModels;

include_once 'BindingModel.php';

class UserChangeProfileBindingModel extends BindingModel
{
    protected $username = '';
    protected $password = '';
    protected $confirm = '';
    protected $newpass = '';

    public function __construct(string $username = null, string $password = null, string $newpass = null, string $confirm = null)
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setNewpass($newpass);
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

    public function getNewpass()
    {
        return $this->newpass;
    }

    public function setNewpass($newpass)
    {
        $this->newpass = $newpass;
    }
}