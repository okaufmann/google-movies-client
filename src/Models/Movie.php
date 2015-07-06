<?php

namespace MightyCode\GoogleMovieClient\Models;

class Movie
{
    private $mid;
    private $name;
    private $info;
    private $imdbLink;

    private $showtimeDays = [];

    /**
     * @return mixed
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * @param mixed $mid
     */
    public function setMid($mid)
    {
        $this->mid = $mid;
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
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return mixed
     */
    public function getImdbLink()
    {
        return $this->imdbLink;
    }

    /**
     * @param mixed $imdbLink
     */
    public function setImdbLink($imdbLink)
    {
        $this->imdbLink = $imdbLink;
    }

    /**
     * @return array
     */
    public function getShowtimeDays()
    {
        return $this->showtimeDays;
    }

    /**
     * @param array $showtimeDays
     */
    public function setShowtimeDays($showtimeDays)
    {
        $this->showtimeDays = $showtimeDays;
    }
}