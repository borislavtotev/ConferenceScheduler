<?php
declare(strict_types=1);

namespace SoftUni\Models;


class Hall
{
    private $id;
    private $venueId;
    private $name;

    public function __construct(int $id = null, int $venueId = null, string $name = null)
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
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setVenueId($venueId)
    {
        $this->venueId = $venueId;
    }

    public function getVenueId()
    {
        return $this->venueId;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}