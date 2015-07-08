<?php

namespace GoogleMovieClient;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use GoogleMovieClient\Helpers\ParseHelper;
use GoogleMovieClient\Models\DataResponse;
use GoogleMovieClient\Models\Movie;
use GoogleMovieClient\Parsers\ShowtimeParser;
use GoogleMovieClient\Parsers\TheaterParser;
use Symfony\Component\DomCrawler\Crawler;

class MovieClient implements MovieClientInterface
{

    private $_baseUrl = "http://www.google.com/movies";

    private $_dev_mode = false;

    //current release of chrome. Got user agent string from: http://www.useragentstring.com/pages/Chrome/
    private $_userAgent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36";

    private $http_client;

    public function __construct()
    {
        $this->constructHttpClient();
    }

    /**
     *  Returns Showtimes for a specific movie near a location
     *
     * @param string $mid
     * @param string $near
     * @param string $lang
     * @return array
     */
    public function getShowtimesByMovieId($mid, $near, $lang = 'en', $dateOffset = 0)
    {
        //http://google.com/movies?near=thun&hl=de&mid=808c5c8cc99039b7

        //TODO: Add multiple result pages parsing

        $dataResponse = $this->getData($near, null, $mid, null, $lang, $dateOffset);
        $days = array();
        if ($dataResponse) {
            $dayDate = Carbon::now();
            $dayDate->setTime(0, 0, 0);

            $parser = new ShowtimeParser($dataResponse->getCrawler());
            $result = $parser->getShowtimeDayByMovie($dayDate->copy());
            $days[] = $result;

            for ($i = $dateOffset + 1; $i < 20; $i++) {
                $dataResponse = $this->getData($near, null, $mid, null, $lang, $i);

                $parser = new ShowtimeParser($dataResponse->getCrawler());
                $result = $parser->getShowtimeDayByMovie($dayDate->addDay(1)->copy());

                if ($result == null) {
                    break;
                } else {
                    $days[] = $result;
                }
            }

        }
        return $days;
    }

    /**
     * Returns Showtimes for a specific Theater
     *
     * @param string $tid
     * @param $near
     * @param string $lang
     * @param int $dateOffset
     * @return mixed
     */
    public function getShowtimesByTheaterId($tid, $near, $lang = 'en', $dateOffset = 0)
    {
        //http://google.com/movies?tid=eef3a3f57d224cf7&hl=de

        //TODO: Add multiple result pages parsing

        $dataResponse = $this->getData($near, null, null, $tid, $lang, $dateOffset);
        $days = array();
        if ($dataResponse) {
            $dayDate = Carbon::now();
            $dayDate->setTime(0, 0, 0);

            $parser = new ShowtimeParser($dataResponse->getCrawler());

            $result = $parser->getShowtimeDayByTheater($dayDate);
            $days[] = $result;

            for ($i = $dateOffset + 1; $i < 20; $i++) {
                $dataResponse = $this->getData($near, null, null, $tid, $lang, $i);

                $parser = new ShowtimeParser($dataResponse->getCrawler());
                $result = $parser->getShowtimeDayByTheater($dayDate->addDay(1)->copy());

                if ($result == null) {
                    break;
                } else {
                    $days[] = $result;
                }
            }
        }

        return $days;
    }

    /**
     * Returns Theaters near a location
     *
     * @param string $near
     * @param string $lang
     * @return mixed
     */
    public function getTheatersNear($near, $lang = 'en')
    {
        //http://google.com/movies?near=thun&hl=de
        //http://google.com/movies?near=thun&hl=de&start=10 (next page)

        $dataResponse = $this->getData($near, null, null, null, $lang);
        $theaters = [];

        if ($dataResponse) {
            $crawler = $dataResponse->getCrawler();
            $parser = new ShowtimeParser($crawler);
            $theaters = $parser->parseTheaters(false);

            $navBarPageLinks = $crawler->filter("#navbar a");
            $furtherPages = $navBarPageLinks->each(function (Crawler $node, $i) {
                return $temp = ParseHelper::getParamFromLink($node->attr("href"), "start");
            });
            $furtherPages = array_unique($furtherPages);

            foreach ($furtherPages as $page) {
                $dataResponse = $this->getData($near, null, null, null, $lang, null, $page);
                if ($dataResponse) {
                    $parser = new ShowtimeParser($crawler);
                    $theaters = array_merge($parser->parseTheaters(false), $theaters);
                }
            }
        }

        return $theaters;

    }

    /**
     * Returns Showtimes found by a search for a movie title
     *
     * @param string $near
     * @param string $name
     * @param string $lang
     * @param null $dateOffset
     * @return mixed
     */
    public function queryShowtimesByMovieTitleNear($near, $name, $lang = 'en', $dateOffset = null)
    {
        // http://google.com/movies?near=Thun&hl=de&q=jurassic+world

        $dataResponse = $this->getData($near, $name, null, null, $lang);
        $days = array();
        if ($dataResponse) {
            $dayDate = Carbon::now();
            $dayDate->setTime(0, 0, 0);
            $crawler = $dataResponse->getCrawler();
            $parser = new ShowtimeParser($crawler);
            $movies = $parser->parseMovies(false);

            //TODO: Replace by handeles multiple movies in results!
            /* @var Movie $movie */
            $movie = $movies[0];

            if (count($movies) > 1) {
                throw new \Exception("more than one movie in search results are not supported yet!");
            } else {
                //Dirty but didn't found better way...
                $midHref = $crawler->filter("#left_nav a")->first()->attr("href");
                $movie->setMid(ParseHelper::getParamFromLink($midHref, "mid"));
            }

            $days[] = $parser->getShowtimeDayByMovie($dayDate->copy());

            for ($i = $dateOffset + 1; $i < 20; $i++) {
                $dataResponse = $this->getData($near, $name, null, null, $lang, $i);

                $parser = new ShowtimeParser($dataResponse->getCrawler());
                $result = $parser->getShowtimeDayByMovie($dayDate->addDay(1)->copy());

                if ($result == null) {
                    break;
                } else {
                    $days[] = $result;
                }
            }
        }

        $movie->setTheaterShowtimeDays($days);

        return $movie;
    }

    /**
     * Returns Showtimes near a location
     *
     * @param string $near
     * @param string $lang
     * @param null $dateOffset
     * @return mixed
     * @throws \Exception
     */
    public function getShowtimesNear($near, $lang = 'en', $dateOffset = null)
    {
        //http://google.com/movies?near=Interlaken&hl=de
        //http://google.com/movies?near=Interlaken&hl=de&start=10
        // TODO: Implement getShowtimesNear() method.
        throw new \Exception("Not implemented yet");
    }

    /**
     * Prepares the http Client
     */
    private function constructHttpClient()
    {
        $this->http_client = new Client();
        $this->http_client->setDefaultOption('headers', ['User-Agent' => $this->_userAgent]);
    }

    /**
     * get the requested html from google with the passed parameters
     *
     * @param null $near
     * @param null $search
     * @param null $mid
     * @param null $tid
     * @param string $language
     * @param null $date
     * @param int $start
     * @return DataResponse
     */
    private
    function getData($near = null, $search = null, $mid = null, $tid = null, $language = "en", $date = null, $start = null)
    {
        $params = array(
            'near' => $near,
            'mid' => $mid,
            'tid' => $tid,
            'q' => $search, //Movie title
            'hl' => $language, //en, de, fr...
            'date' => $date,
            'start' => $start
        );

        $response = new DataResponse();
        if ($this->_dev_mode) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/google-movie-client/testdata/movies_' . http_build_query($params) . '.html';
            $response->body = file_get_contents($url);
        } else {
            $url = $this->_baseUrl . '?' . http_build_query($params);

            $guzzle_response = $this->http_client->get($url);

            $response->body = $guzzle_response->getBody()->getContents();
            $response->code = $guzzle_response->getStatusCode();
            $response->headers = $guzzle_response->getHeaders();
        }


        return $response;
    }
}