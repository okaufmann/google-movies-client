<?php

namespace GoogleMovieClient;

interface ClientInterface
{

    /**
     *  Returns Showtimes for a specific movie near a location
     *
     * @param string $mid
     * @param string $near
     * @param string $lang
     * @param null $dateOffset
     * @return mixed
     */
    public function getShowtimesByMovieId($mid, $near, $lang = 'en', $dateOffset = null);

    /**
     * Returns Showtimes for a specific Theater
     *
     * @param string $tid
     * @param $near
     * @param string $lang
     * @param null $dateOffset
     * @return mixed
     */
    public function getShowtimesByTheaterId($tid, $near, $lang = 'en', $dateOffset = null);

    /**
     * Returns Theaters near a location
     *
     * @param string $near
     * @param string $lang
     * @return mixed
     */
    public function getTheatersNear($near, $lang = 'en');

    /**
     * Returns Showtimes near a location
     *
     * @param string $near
     * @param string $lang
     * @param null $dateOffset
     * @return mixed
     */
    public function getShowtimesNear($near, $lang = 'en', $dateOffset = null);

    /**
     * Returns Showtimes found by a search for a movie title
     *
     * @param string $near
     * @param string $name
     * @param string $lang
     * @return mixed
     */
    public function queryShowtimesByMovieTitleNear($near, $name, $lang = 'en', $dateoffset = null);
}
