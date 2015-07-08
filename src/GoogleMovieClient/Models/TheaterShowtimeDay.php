<?php

namespace GoogleMovieClient\Models;

class TheaterShowtimeDay
{
    private $date;
    
    private $theaters = [];

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getTheaters()
    {
        return $this->theaters;
    }

    /**
     * @param array $theaters
     */
    public function setTheaters($theaters)
    {
        $this->theaters = $theaters;
    }
}