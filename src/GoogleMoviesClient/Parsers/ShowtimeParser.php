<?php

namespace GoogleMoviesClient\Parsers;

use Carbon\Carbon;
use GoogleMoviesClient\Helpers\ParseHelper;
use GoogleMoviesClient\Models\MovieShowtimeDay;
use GoogleMoviesClient\Models\ShowtimeInfo;
use GoogleMoviesClient\Models\TheaterShowtimeDay;
use Symfony\Component\DomCrawler\Crawler;

class ShowtimeParser extends ParserAbstract
{
    /**
     * ShowtimeParser constructor.
     *
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
     * Parses the Movies from the result div.
     *
     * @param bool $includeShowtimes
     *
     * @return array|null
     */
    public function parseMovies($includeShowtimes = true)
    {
        $movieDivs = $this->crawler->filter('#movie_results .movie');

        $count = $movieDivs->count();

        if ($count == 0) {
            return null;
        }

        if ($count = 1) {
            $justOneMovieFound = true;
        }

        $movies = $movieDivs->each(function (Crawler $movieDiv, $i) use (
            $includeShowtimes,
            $justOneMovieFound
        ) {

            $resultItemParser = new ResultItemParser($movieDiv);

            if ($justOneMovieFound) {
                //extract url with mid from
                $firstLeftNavLink = $this->getFirstLeftNavLink();
                $fallbackUrl = $firstLeftNavLink->attr('href');
                $movie = $resultItemParser->parseResultMovieItem($fallbackUrl);
            } else {
                $movie = $resultItemParser->parseResultMovieItem();
            }

            if ($movie == null) {
                return null;
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

    /**
     * Parses the Theaters from the result div.
     *
     * @param bool $includeShowtimes
     * @return array|null
     */
    public function parseTheaters($includeShowtimes = true)
    {
        $theatersDivs = $this->crawler->filter('#movie_results .theater');

        $count = $theatersDivs->count();

        if ($count == 0) {
            return null;
        }

        if ($count = 1) {
            $justOneTheaterFound = true;
        }

        $theaters = $theatersDivs->each(function (Crawler $theaterDiv, $i) use (
            $includeShowtimes,
            $justOneTheaterFound
        ) {

            $resultItemParser = new ResultItemParser($theaterDiv);

            if ($justOneTheaterFound) {
                $firstLeftNavLink = $this->getFirstLeftNavLink();
                $fallbackUrl = $firstLeftNavLink->attr('href');
                $theater = $resultItemParser->parseResultTheaterItem($fallbackUrl);
            } else {
                $theater = $resultItemParser->parseResultTheaterItem();
            }

            if (! $theater) {
                return null;
            }

            if ($includeShowtimes) {
                $theater->setShowtimeInfo($this->parseShowtimeInfo($theaterDiv));
            }

            return $theater;
        });

        $theaters = array_filter($theaters, function ($item) {
            return $item != null;
        });

        return $theaters;
    }

    /**
     * Gets the first link from the left nav.
     *
     * @return Crawler
     */
    public function getFirstLeftNavLink()
    {
        return $this->crawler->filter('#left_nav .section a')->first();
    }

    /**
     * Gets Showtime Infos from a div.
     *
     * @param Crawler $resultDiv
     * @return ShowtimeInfo
     */
    private function parseShowtimeInfo(Crawler $resultDiv)
    {
        $showtimeSpans = $resultDiv->filter('.times')->first();

        return $this->parseShowtime($showtimeSpans);
    }

    /**
     * Parses showtime infos from a individual div.
     *
     * @param Crawler $timeSpan
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

            if (! empty($time)) {
                $times[] = $time;
            }
        }

        $showtime->setTimes(array_unique($times));

        return $showtime;
    }

    /**
     * Parses the Time out of the times string.
     *
     * @param $input
     * @return null
     */
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
