<?php

namespace MightyCode\GoogleMovieClient;

interface MovieClientInterface
{

    /**
     *  Returns Showtimes for a specific movie near a location
     *
     * @param string $mid
     * @param string $near
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getShowtimesByMovieId($mid, $near, $lang = 'en');


    /**
     * Returns Showtimes for a specific Theater
     *
     * @param string $tid
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getShowtimesByTheaterId($tid, $near, $lang = 'en');


    /**
     * Returns Theaters near a location
     *
     * @param string $near
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getTheatersNear($near, $lang = 'en');

    /**
     * Returns Showtimes near a location
     *
     * @param string $near
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getShowtimesNear($near, $lang = 'en');

    /**
     * Returns Showtimes found by a search for a movie title
     *
     * @param string $near
     * @param string $name
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function queryShowtimesByMovieTitleNear($near, $name, $lang = 'en');

}