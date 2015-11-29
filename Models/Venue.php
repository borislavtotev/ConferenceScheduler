<?php
declare(strict_types=1);

namespace SoftUni\Models;


class Venue
{
    private $id;
    private $name;
    private $address;

    public function __construct(int $id = null, string $name = null, string $address = null )
    {
        if ($id != null) {
            $this->setId($id);
        }

        if ($name != null) {
            $this->setName($name);
        }

        if ($address != null) {
            $this->setAddress($address);
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

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    public function getAddress() :string
    {
        return $this->address;
    }
}