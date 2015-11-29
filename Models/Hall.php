<?php
declare(strict_types=1);

namespace SoftUni\Models;


class Hall
{
    private $id;
    private $venueId;
    private $name;
    private $userLimit;

    public function __construct(int $id = null, int $venueId = null, string $name = null, int $userLimit)
    {
        if ($id != null) {
            $this->setId($id);
        }

        if ($venueId != null) {
            $this->setVenueId($venueId);
        }

        if ($name != null) {
            $this->setName($name);
        }

        if ($userLimit != null) {
            $this->setUserLimit($userLimit);
        }
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId() :int
    {
        return $this->id;
    }

    public function setVenueId($venueId)
    {
        $this->venueId = $venueId;
    }

    public function getVenueId() :int
    {
        return $this->venueId;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function setUserLimit(int $userLimit)
    {
        $this->userLimit = $userLimit;
    }

    public function getUserLimit() :int
    {
        return $this->userLimit;
    }
}