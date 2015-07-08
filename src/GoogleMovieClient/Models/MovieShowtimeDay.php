<?php

namespace GoogleMovieClient\Models;

class MovieShowtimeDay
{
    private $date;

    private $movies = [];

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
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * @param array $movies
     */
    public function setMovies($movies)
    {
        $this->movies = $movies;
    }
}
