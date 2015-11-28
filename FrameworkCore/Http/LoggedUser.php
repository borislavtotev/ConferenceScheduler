<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore\Http;

class LoggedUser
{
    private $username;
    private $id;

    public function __construct(int $id = null, string $username = null)
    {
        if ($id != null) {
            $this->setId($id);
        } else if (isset($_SESSION['userId'])) {
            $this->setId($_SESSION['userId']);
        }

        if ($username != null) {
            $this->setUsername($username);
        } else if (isset($_SESSION['username'])) {
            $this->setUsername($_SESSION['username']);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id = null)
    {
        $this->id = $id;
        $_SESSION['userId'] = $id;

    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
        $_SESSION['username'] = $username;
    }
}