<?php

namespace GoogleMoviesClient\Models;

class Movie
{
    private $mid;
    private $name;
    private $info;
    private $imdbLink;
    private $theaterShowtimeDays;

    /**
     * @var ShowtimeInfo
     */
    private $showtimeInfo;

    public function __construct(ResultItem $resultItem = null)
    {
        if ($resultItem != null) {
            $this->setName($resultItem->getName());
            $this->setInfo($resultItem->getInfo());
            $this->setMid($resultItem->getId());
        }
    }

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

    /**
     * @return array
     */
    public function getTheaterShowtimeDays()
    {
        return $this->theaterShowtimeDays;
    }

    /**
     * @param array $theaterShowtimeDays
     */
    public function setTheaterShowtimeDays($theaterShowtimeDays)
    {
        $this->theaterShowtimeDays = $theaterShowtimeDays;
    }
}
