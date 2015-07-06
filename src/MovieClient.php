<?php

namespace MightyCode\GoogleMovieClient;


class MovieClient implements \MovieClientInterface
{

    private static $_baseUrl = "http://www.google.com/movies";

    //current release of chrome. Got user agent string from: http://www.useragentstring.com/pages/Chrome/
    private $_userAgent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36";

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
    public function getShowtimesByMovieId($mid, $near, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?near=thun&hl=de&mid=808c5c8cc99039b7
        // TODO: Implement getShowtimesByMovieId() method.
    }

    /**
     * Returns Showtimes for a specific Theater
     *
     * @param string $tid
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getShowtimesByTheaterId($tid, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?hl=de&tid=eef3a3f57d224cf7
        // TODO: Implement getShowtimesByTheaterId() method.
    }

    /**
     * Returns Theaters near a location
     *
     * @param string $near
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getTheatersNear($near, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?near=thun&hl=de
        //http://google.com/movies?near=thun&hl=de&start=10 (next page)

        // TODO: Implement getTheatersNear() method.
    }

    /**
     * Returns Showtimes near a location
     *
     * @param string $near
     * @param string $lang
     * @param int $date
     * @param int $start
     * @return mixed
     */
    public function getShowtimesNear($near, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?near=Interlaken&hl=de
        //http://google.com/movies?near=Interlaken&hl=de&start=10
        // TODO: Implement getShowtimesNear() method.
    }

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
    public function queryShowtimesByMovieTitleNear($near, $name, $lang = 'en', $date = 0, $start = 0)
    {
        // http://google.com/movies?near=Thun&hl=de&q=jurassic+world
        // TODO: Implement queryShowtimesByMovieTitleNear() method.
    }
}