<?php

namespace MightyCode\GoogleMovieClient\Models;

class Theater
{
    private $tid;
    private $name;
    private $address;

    /**
     * @var ShowtimeInfo
     */
    private $showtimeInfo;

    public function __construct(ResultItem $resultItem = null)
    {
        if ($resultItem != null) {
            $this->setName($resultItem->getName());
            $this->setAddress($resultItem->getInfo());
            $this->setTid($resultItem->getId());
        }
    }

    /**
     * @return mixed
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * @param mixed $tid
     */
    public function setTid($tid)
    {
        $this->tid = $tid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return ShowtimeInfo
     */
    public function getShowtimeInfo()
    {
        return $this->showtimeInfo;
    }

    /**
     * @param ShowtimeInfo $showtimeInfo
     */
    public function setShowtimeInfo($showtimeInfo)
    {
        $this->showtimeInfo = $showtimeInfo;
    }
}