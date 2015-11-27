<?php
declare(strict_types=1);

namespace SoftUni\Models\BindingModels;

class UserBindingModel
{
    private $username = '';
    private $password = '';
    private $confirm = '';
    private $isValid = '';

    public function __construct(string $username = null, string $password = null, string $confirm = null)
    {
        if ($username == null) {
            if (isset($_POST['username'])) {
                $this->setUsername($_POST['username']);
            } else {
                $this->setIsValid(false);
            }
        } else {
            $this->setUsername($username);
        }

        if ($password == null) {
            if (isset($_POST['password'])) {
                $this->setPassword($_POST['password']);
            } else {
                $this->setIsValid(false);
            }
        } else {
            $this->setPassword($password);
        }

        if ($confirm == null) {
            if (isset($_POST['confirm'])) {
                $this->setConfirm($_POST['confirm']);
            } else {
                $this->setIsValid(false);
            }
        } else {
            $this->setConfirm($confirm);
        }
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

    public function getIsValid()
    {
        return $this->isValid;
    }

    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
    }
}