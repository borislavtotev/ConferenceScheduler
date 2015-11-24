<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 11/24/2015
 * Time: 10:08 AM
 */

namespace SoftUni\FrameworkCore;


class DatabaseContext
{
    private $identityUsersRepository;
    private $repositories = [];

    /**
     * DatabaseContext constructor.
     * @param $usersRepository
     */
    public function __construct($usersRepository)
    {
        $this->identityUsersRepository = $usersRepository;
        $this->repositories[] = $this->identityUsersRepository;
    }

    /**
     * @return \SoftUni\FrameworkCore\Repositories\IdentityUsersRepository
     */
    public function getIdentityUsersRepository()
    {
        return $this->identityUsersRepository;
    }

    /**
     * @param mixed $identityUsersRepository
     * @return $this
     */
    public function setIdentityUsersRepository($identityUsersRepository)
    {
        $this->identityUsersRepository = $identityUsersRepository;
        return $this;
    }

    public function saveChanges()
    {
        foreach ($this->repositories as $repository) {
            $repositoryName = get_class($repository);
            $repositoryName::save();
        }
    }
}