<?php

namespace SoftUni\Application\Areas\Users\ViewModels;

class UserViewModel
{
    private $id;
    private $user;
    private $pass;
    private $gold;
    private $food;

    public function __construct($user, $pass, $id = null, $gold = null, $food = null)
    {
        $this->setId($id)
            ->setUsername($user)
            ->setPass($pass)
            ->setGold($gold)
            ->setFood($food);
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
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return $this
     */
    public function setUsername($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param mixed $pass
     * @return $this
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGold()
    {
        return $this->gold;
    }

    /**
     * @param mixed $gold
     * @return $this
     */
    public function setGold($gold)
    {
        $this->gold = $gold;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFood()
    {
        return $this->food;
    }

    /**
     * @param mixed $food
     * @return $this
     */
    public function setFood($food)
    {
        $this->food = $food;
        return $this;
    }
}