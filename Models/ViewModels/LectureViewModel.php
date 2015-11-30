<?php
declare(strict_types=1);

namespace SoftUni\Models\ViewModels;

class LectureViewModel
{
    private $lecture;
    private $starttime;
    private $endtime;
    private $hall;
    private $speker;

    public function getLecture()
    {
        return $this->lecture;
    }

    public function setLecture($lecture)
    {
        $this->lecture = $lecture;
    }

    public function getHall()
    {
        return $this->hall;
    }

    public function setHall($hall)
    {
        $this->hall = $hall;
    }

    public function getEndtime()
    {
        return $this->endtime;
    }

    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    public function getSpeker()
    {
        return $this->speker;
    }

    public function setSpeker($speker)
    {
        $this->speker = $speker;
    }

    public function getStarttime()
    {
        return $this->starttime;
    }

    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }
}