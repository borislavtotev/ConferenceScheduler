<?php
declare(strict_types=1);

namespace SoftUni\Models;


class Lecture
{
    private $id;
    private $startDateTime;
    private $endDateTime;
    private $hallId;
    private $speakerId;
    private $name;

    public function __construct(int $id = null, string $startDateTime = null, string $endDataTime = null,
                                int $hallId = null, int $speakerId = null, string $name)
    {
        if ($id != null) {
            $this->setId($id);
        }

        if ($startDateTime != null) {
            $this->setStartDateTime($startDateTime);
        }

        if ($endDataTime != null) {
            $this->setEndDateTime($endDataTime);
        }

        if ($hallId != null) {
            $this->setHallId($hallId);
        }

        if ($speakerId != null) {
            $this->setSpeakerId($speakerId);
        }

        if ($name != null) {
            $this->setName($name);
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

    public function setStartDateTime(string $startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    public function getStartDateTime() :string
    {
        return $this->startDateTime;
    }

    public function setEndDateTime(string $endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    public function getEndDateTime() :string
    {
        return $this->endDateTime;
    }

    public function setHallId(int $hallId)
    {
        $this->hallId = $hallId;
    }

    public function getHallId() :int
    {
        return $this->hallId;
    }

    public function setSpeakerId(int $speakerId)
    {
        $this->speakerId = $speakerId;
    }

    public function getSpeakerId() :int
    {
        return $this->speakerId;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName() :string
    {
        return $this->name;
    }
}