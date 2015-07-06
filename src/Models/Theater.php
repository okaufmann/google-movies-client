<?php

namespace MightyCode\GoogleMovieClient\Models;

class Theater
{
    private $tid;
    private $name;
    private $address;

    private $showtimes = [];

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
     * @return array
     */
    public function getShowtimes()
    {
        return $this->showtimes;
    }

    /**
     * @param array $showtimes
     */
    public function setShowtimes($showtimes)
    {
        $this->showtimes = $showtimes;
    }
}