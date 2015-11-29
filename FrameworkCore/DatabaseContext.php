<?php
declare(strict_types=1);

namespace SoftUni\FrameworkCore;

class DatabaseContext
{
    private $identityUsersRepository;
    private $repositories = [];
    private $conferencesRepository;

    /**
     * DatabaseContext constructor.
     * @param $usersRepository
     */
    public function __construct($usersRepository, $conferenceRepository, $hallRepository, $lecturesRepository, $venuesRepository)
    {
        $this->identityUsersRepository = $usersRepository;
        $this->repositories[] = $this->identityUsersRepository;

        $this->conferencesRepository = $conferenceRepository;
        $this->repositories[] = $this->conferencesRepository;

        $this->hallRepository = $hallRepository;
        $this->repositories[] = $this->hallRepository;

        $this->lecturesRepository = $lecturesRepository;
        $this->repositories[] = $this->lecturesRepository;

        $this->venuesRepository = $venuesRepository;
        $this->repositories[] = $this->venuesRepository;
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

    /**
     * @return \SoftUni\FrameworkCore\Repositories\ConferencesRepository
     */
    public function getConferencesRepository()
    {
        return $this->conferencesRepository;
    }

    /**
     * @param mixed $ConferencesRepository
     * @return $this
     */
    public function setConferencesRepository($confurencesRepository)
    {
        $this->identityUsersRepository = $confurencesRepository;
        return $this;
    }

    public function setHallRepository($hallRepository)
    {
        $this->hallRepository = $hallRepository;
    }

    public function getHallRepository()
    {
        return $this->hallRepository;
    }

    public function setVenuesRepository($venuesRepository)
    {
        $this->venuesRepository = $venuesRepository;
    }

    public function getVenuesRepository()
    {
        return $this->venuesRepository;
    }

    public function setLecturesRepository($lecturesRepository)
    {
        $this->lecturesRepository = $lecturesRepository;
    }

    public function getLecturesRepository()
    {
        return $this->lecturesRepository;
    }

    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
    }

    public function getRepositories()
    {
        return $this->repositories;
    }

    public function saveChanges()
    {
        foreach ($this->repositories as $repository) {
            $repositoryName = get_class($repository);
            $repositoryName::save();
        }
    }
}