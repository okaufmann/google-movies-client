<?php
namespace MightyCode\GoogleMovieClient\Parsers;

use Carbon\Carbon;
use MightyCode\GoogleMovieClient\Helpers\ParseHelper;
use MightyCode\GoogleMovieClient\Models\MovieShowtimeDay;
use MightyCode\GoogleMovieClient\Models\ShowtimeInfo;
use MightyCode\GoogleMovieClient\Models\TheaterShowtimeDay;
use MightyCode\GoogleMovieClient\Models\Theater;
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
     * Returns all Days of Showtimes by a Movie
     *
     * @param Carbon $date
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
     * Returns all Days of Showtimes by a Theater
     * @param Carbon $date
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

    private function parseMovies()
    {
        $movies = [];

        $movieDivs = $this->crawler->filter('#movie_results .movie');

        $count = $movieDivs->count();
        if ($count == 0) {
            return null;
        }

        foreach ($movieDivs as $i => $contents) {
            $movieDiv = new Crawler($contents);

            $resultItemParser = new ResultItemParser($movieDiv);

            $movie = $resultItemParser->parseResultMovieItem();
            if ($movie == null) {
                break;
            }
            $movie->setShowtimeInfo($this->parseShowtimeInfo($movieDiv));

            $movies[] = $movie;
        }

        return $movies;
    }

    private function parseTheaters()
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
            $theater->setShowtimeInfo($this->parseShowtimeInfo($theaterDiv));

            $theaters[] = $theater;
        }

        return $theaters;
    }


    private function parseShowtimeInfo(Crawler $theaterDiv)
    {
        $showTimes = [];
        $showtimeSpans = $theaterDiv->filter(".times");
        foreach ($showtimeSpans as $j => $timeSpanContent) {

            $showtime = $this->parseShowtime($timeSpanContent);

            $showTimes[] = $showtime;
        }

        return $showTimes;
    }

    /**
     * @param $timeSpanContent
     * @param $matches
     * @return ShowtimeInfo
     */
    private function parseShowtime($timeSpanContent)
    {
        $showtime = new ShowtimeInfo();

        $timeSpan = new Crawler($timeSpanContent);
        $texts = explode(" ", $timeSpan->text());

        $showtime->setInfo($texts[0]);

        $times = [];
        foreach ($texts as $text) {
            $time = trim(html_entity_decode($text));
            $time = str_replace("&nbsp", "", $time);

            preg_match("/[0-9][0-9]:[0-9][0-9]/", $time, $matches);
            if (count($matches) > 0) {
                $times[] = $matches[0];
            }
        }

        $showtime->setTimes($times);

        return $showtime;
    }
}

