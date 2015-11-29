<?php
declare(strict_types=1);

namespace SoftUni\Models;


class Conference
{
    private $ownerId;
    private $administratorId;
    private $id;
    private $venueId;
    private $name;

    public function __construct(int $ownerId = null, int $administratorId = null, int $id = null, int $venueId = null, string $name = null )
    {
        if ($id != null) {
            $this->setId($id);
        }

        if ($ownerId != null) {
            $this->setOwnerId($ownerId);
        }

        if ($venueId != null) {
            $this->setVenueId($venueId);
        }

        if ($name != null) {
            $this->setName($name);
        }

        if ($administratorId != null) {
            $this->setAdministratorId($administratorId);
        }
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId() :int
    {
        return $this->id;
    }

    public function setOwnerId(int $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    public function getOwnerId() :int
    {
        return $this->ownerId;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function setVenueId(int $venueId)
    {
        $this->venueId = $venueId;
    }

    public function getVenueId() :int
    {
        return $this->venueId;
    }

    public function setAdministratorId(int $administratorId)
    {
        $this->administratorId = $administratorId;
    }

    public function getAdministratorId() :int
    {
        return $this->administratorId;
    }
}