<?php
namespace MightyCode\GoogleMovieClient\Parsers;

use Carbon\Carbon;
use MightyCode\GoogleMovieClient\Helpers\ParseHelper;
use MightyCode\GoogleMovieClient\Models\ShowtimeInfo;
use MightyCode\GoogleMovieClient\Models\ShowtimeDay;
use MightyCode\GoogleMovieClient\Models\Theater;
use Symfony\Component\DomCrawler\Crawler;

class ShowtimeParser
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Returns all Days
     *
     * @return array
     */
    public function getShowtimeDay(Carbon $date)
    {
        $showDay = new ShowtimeDay();
        $theaters = $this->parseTheaters();
        if (count($theaters) > 0) {
            $showDay->setTheaters($theaters);
            $showDay->setDate($date->copy());
            return $showDay;
        }

        return null;
    }

    private function parseTheaters()
    {
        $theaters = [];

        $theatersDiv = $this->crawler->filter('#movie_results .theater');

        $count = $theatersDiv->count();
        if ($count == 0) {
            return null;
        }

        foreach ($theatersDiv as $i => $contents) {

            $theaterDiv = new Crawler($contents);

            $theater = $this->parseTheater($theaterDiv);
            if ($theater == null) {
                break;
            }
            $theater->setShowtimeInfo($this->parseShowtimeInfo($theaterDiv));

            $theaters[] = $theater;
        }

        return $theaters;
    }

    private function parseTheater(Crawler $theaterDiv)
    {
        $theaterHref = $theaterDiv->filter(".name a")->first();

        $url = $theaterHref->attr("href");

        if (!$url) {
            return null;
        }

        $theater = new Theater();
        $theater->setTid(ParseHelper::getParamFromLink($url, "tid"));
        $theater->setName($theaterHref->text());
        $theater->setAddress(strip_tags($theaterDiv->filter(".address")->first()->text()));

        return $theater;
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
     * @return Crawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * @param Crawler $crawler
     */
    public function setCrawler($crawler)
    {
        $this->crawler = $crawler;
    }

    private function parseMovies()
    {
        return $this->crawler->filter('#movie_results .movie');
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