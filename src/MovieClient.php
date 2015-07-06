<?php

namespace MightyCode\GoogleMovieClient;


class MovieClient implements \MovieClientInterface
{

    public function getShowtimesByMovieId($mid, $near, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?near=thun&hl=de&mid=808c5c8cc99039b7
        // TODO: Implement getShowtimesByMovieId() method.
    }

    public function getShowtimesByTheaterId($tid, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?hl=de&tid=eef3a3f57d224cf7
        // TODO: Implement getShowtimesByTheaterId() method.
    }

    public function getTheatersNear($near, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?near=thun&hl=de
        //http://google.com/movies?near=thun&hl=de&start=10 (next page)

        // TODO: Implement getTheatersNear() method.
    }

    public function getShowtimesNear($near, $lang = 'en', $date = 0, $start = 0)
    {
        //http://google.com/movies?near=Interlaken&hl=de
        //http://google.com/movies?near=Interlaken&hl=de&start=10
        // TODO: Implement getShowtimesNear() method.
    }
}