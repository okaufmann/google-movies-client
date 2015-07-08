<?php

namespace GoogleMovieClient\Parsers;

use Carbon\Carbon;
use GoogleMovieClient\Helpers\ParseHelper;
use GoogleMovieClient\Models\MovieShowtimeDay;
use GoogleMovieClient\Models\ShowtimeInfo;
use GoogleMovieClient\Models\TheaterShowtimeDay;
use Symfony\Component\DomCrawler\Crawler;

class ShowtimeParser extends ParserAbstract
{
    /**
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Returns all Days of Showtimes by a Movie.
     *
     * @param Carbon $date
     *
     * @return TheaterShowtimeDay|null
     */
    public function getShowtimeDayByMovie(Carbon $date)
    {
        $showDay = new TheaterShowtimeDay();
        $theaters = $this->parseTheaters();
        if (count($theaters) > 0) {
            $showDay->setTheaters($theaters);
            $showDay->setDate($date);

            return $showDay;
        }

        return null;
    }

    /**
     * Returns all Days of Showtimes by a Theater.
     *
     * @param Carbon $date
     *
     * @return MovieShowtimeDay|null
     */
    public function getShowtimeDayByTheater(Carbon $date)
    {
        $showDay = new MovieShowtimeDay();
        $movies = $this->parseMovies();
        if (count($movies) > 0) {
            $showDay->setMovies($movies);
            $showDay->setDate($date);

            return $showDay;
        }

        return null;
    }

    /**
     * @param bool $includeShowtimes
     *
     * @return array|null
     */
    public function parseMovies($includeShowtimes = true)
    {
        $movies = [];

        $movieDivs = $this->crawler->filter('#movie_results .movie');

        $count = $movieDivs->count();
        if ($count == 0) {
            return null;
        }

        $movies = $movieDivs->each(function (Crawler $movieDiv, $i) use ($includeShowtimes) {

            $resultItemParser = new ResultItemParser($movieDiv);

            $movie = $resultItemParser->parseResultMovieItem();
            if ($movie == null) {
                return;
            }

            $movieInfoLinks = $movieDiv->filter('.info a');
            if (count($movieInfoLinks) > 0) {
                $imdbLink = $movieInfoLinks->last();
                if ($imdbLink != null) {
                    $movie->setImdbLink(ParseHelper::getParamFromLink($imdbLink->attr('href'), 'q'));
                }
            }

            if ($includeShowtimes) {
                $movie->setShowtimeInfo($this->parseShowtimeInfo($movieDiv));
            }

            return $movie;
        });

        return $movies;
    }

    public function parseTheaters($includeShowtimes = true)
    {
        $theaters = [];

        $theatersDivs = $this->crawler->filter('#movie_results .theater');

        $count = $theatersDivs->count();
        if ($count == 0) {
            return null;
        }

        foreach ($theatersDivs as $i => $contents) {
            $theaterDiv = new Crawler($contents);

            $resultItemParser = new ResultItemParser($theaterDiv);

            $theater = $resultItemParser->parseResultTheaterItem();
            if ($theater == null) {
                break;
            }

            if ($includeShowtimes) {
                $theater->setShowtimeInfo($this->parseShowtimeInfo($theaterDiv));
            }

            $theaters[] = $theater;
        }

        return $theaters;
    }

    private function parseShowtimeInfo(Crawler $resultDiv)
    {
        $showtimeSpans = $resultDiv->filter('.times')->first();

        return $this->parseShowtime($showtimeSpans);
    }

    /**
     * @param $timeSpanContent
     * @param $matches
     *
     * @return ShowtimeInfo
     */
    private function parseShowtime(Crawler $timeSpan)
    {
        $showtime = new ShowtimeInfo();

        $texts = explode(' ', str_replace('&nbsp', '', $timeSpan->text()));

        if ($this->getTime($texts[0]) == null) {
            $showtime->setInfo($texts[0]);
        }

        $times = [];
        foreach ($texts as $text) {
            $time = trim(html_entity_decode($text));
            $time = $this->getTime($time);

            if (!empty($time)) {
                $times[] = $time;
            }
        }

        $showtime->setTimes($times);

        return $showtime;
    }

    private function getTime($input)
    {
        //test 12 h format
        preg_match('/(1[012]|[1-9]):[0-5][0-9](\\s)?(?i)(am|pm)/', $input, $matches);
        if (count($matches) > 0) {
            return $matches[0];
        }

        //test 24 h format
        preg_match('/([01]?[0-9]|2[0-3]):[0-5][0-9]/', $input, $matches);
        if (count($matches) > 0) {
            return $matches[0];
        }

        return null;
    }
}
