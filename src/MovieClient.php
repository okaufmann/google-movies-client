<?php

namespace MightyCode\GoogleMovieClient;

use MightyCode\GoogleMovieClient\Models\DataResponse;
use MightyCode\GoogleMovieClient\Models\Movie;
use MightyCode\GoogleMovieClient\Models\Showtime;
use MightyCode\GoogleMovieClient\Models\ShowtimeDay;
use MightyCode\GoogleMovieClient\Models\Theater;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;


class MovieClient
{
    private $_baseUrl = "http://www.google.com/movies";

    //current release of chrome. Got user agent string from: http://www.useragentstring.com/pages/Chrome/
    private $_userAgent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36";

    /**
     * search for a movie title an get showtimes
     * @param $near
     * @param $search
     * @param string $lang
     * @return array
     */
    public function findShowtimesByMovieTitle($near, $search, $lang = 'en')
    {
        $movieList = array();

        $objDateTime = new \DateTime('NOW');

        $dataResponse = $this->getData($near, $search, null, null, null, $lang);

        $crawledMovies = $this->crawlMovies($dataResponse);
        //$parsedMovies = $this->parseMovies($dataResponse);

        if (count($parsedMovies) == 0) {
            return $movieList;
        }

        $dateText = $objDateTime->format("Y-m-d");

        $movieList[$dateText] = $parsedMovies;

        $firstsection = $dataResponse->dom()->find("#left_nav .section", 1);
        foreach ($firstsection->find("a") as $date) {

            $objDateTime->add(new \DateInterval('P1D'));

            $dateText = $objDateTime->format("Y-m-d");

            $date = $this->getParamFromLink($date->attr["href"], "date");

            $dataResponse = $this->getData($near, $search, null, null, $date, $lang);

            $movieList[$dateText] = $this->parseMovies($dataResponse);
        }

        return $movieList;
    }

    /**
     * get next showtimes by Movie ID
     *
     * @param $near
     * @param $mid
     * @param string $lang
     * @return Movie
     */
    public function getShowtimesByMovieId($near, $mid, $lang = 'en')
    {
        //prepare return val
        $movie = null;

        //prepare day movies showed
        $showtimeDays = array();

        //get dateime from now
        $objDateTime = new \DateTime('NOW');

        //access google to fetch data
        $htmlDom = $this->getData($near, null, $mid, null, null, $lang);

        //parse result
        $parsedMovies = $this->parseMovies($htmlDom);

        if ($parsedMovies == null || count($parsedMovies) == 0) {
            return null;
        }

        $movie = $parsedMovies[0];

        $firstsection = $htmlDom->dom()->find("#left_nav .section", 1);
        foreach ($firstsection->find("a") as $date) {

            $objDateTime->add(new \DateInterval('P1D'));

            $date = $this->getParamFromLink($date->attr["href"], "date");

            $htmlDom = $this->getData($near, null, $mid, null, $date, $lang);

            $parsedMovies = $this->parseMovies($htmlDom);

            if ($parsedMovies == null || count($parsedMovies) == 0) {
                return null;
            }

            $showtimeDays[] = $this->buildShowtimeDay($parsedMovies[0], $objDateTime);
        }

        $movie->showtimeDays = $showtimeDays;
        $movie->theaters = null;

        return $movie;
    }


    public function getMovieShowtimesNear($near, $lang = "en")
    {
        //TODO
    }

    /**
     * extract the showtimes of a parsed movie
     * @param $parsedMovie
     * @param $objDateTime
     * @return ShowtimeDay
     */
    private function buildShowtimeDay($parsedMovie, $objDateTime)
    {
        $showtimeDay = new ShowtimeDay();
        $showtimeDay->date = $objDateTime->format("Y-m-d");
        $showtimeDay->theaters = $parsedMovie->theaters;

        return $showtimeDay;
    }

    private function crawlMovies(DataResponse $response)
    {
        $movies = array();
        $multipleFound = false;
        $crawler = $response->getCrawler();
        $mid = null;
        $movieDivs = $crawler->filter("#movie_results .movie");

        if (count($movieDivs) === 0) {
            return $movies;
        }

        if (count($movieDivs) > 1) {
            $multipleFound = true;
        } else {
            $midLink = $crawler->filter("#left_nav .section a")->first()->attr("href");
            $mid = $this->getParamFromLink($midLink, "mid");
        }

        foreach ($movieDivs as $i => $contents) {
            $movie = new Movie();
            $movieDiv = new Crawler($contents);

            if ($multipleFound) {
                $movie->mid = $this->tryGetMid($movieDiv);
                $movie->name = $this->tryGetMovieTitle($movieDiv);
            } else {
                $movie->name = $this->tryGetMovieTitle($movieDiv);
                $movie->mid = $mid;
            }

            $infoDivs = $movieDiv->filter("div.info");
            if (count($infoDivs) > 1) {
                $movie->info = join(" ", $infoDivs[1]->text());
                $links = $movieDiv->filter("div.links a");
                $movie->imdbLink = $this->getParamFromLink($links[count($links) - 1]->attr["href"], "q");
            } else {
                $movie->info = join(" ", $movieDiv->filter("div.info")->first()->text());
            }

            foreach ($movieDiv->filter(".theater") as $y => $theaterDivContent) {

                $theaterDiv = new Crawler($theaterDivContent);

                $theaterHref = $theaterDiv->filter(".name a")->first();

                $theater = new Theater();
                $theater->tid = $this->getParamFromLink($theaterHref->attr("href"), "tid");
                $theater->name = $theaterHref->text();
                $theater->address = strip_tags($theaterAddress = $theaterDiv->filter(".address")->first()->text());

                foreach ($theaterDiv->filter(".times") as $j => $timeSpanContent) {

                    $timeSpan = new Crawler($timeSpanContent);
                    $texts = explode(" ",$timeSpan->text());

                    $showtime = new Showtime();
                    $showtime->info = $texts[0];

                    foreach ($texts as $text) {
                        $time = trim(html_entity_decode($text));

                        preg_match("/^[0-9]{1,2}:[0-9]{1,2}/", $time, $matches);
                        if (count($matches) > 0) {
                            $showtime->times[] = $matches[0];
                        }
                    }

                    $theater->showtimes[] = $showtime;
                }

                $movie->theaters[] = $theater;
            }

            $movies[] = $movie;
        }

    }

    /**
     * parse the html and return the found movies with all information as Objects
     * @param $htmlDom
     * @return array
     * @throws \Exception
     */
    private function parseMovies(DataResponse $htmlDom)
    {
        $multipleFound = false;
        $mid = null;
        $movies = array();

        $movieDivs = $htmlDom->dom()->find("#movie_results .movie");
        if (count($movieDivs) === 0) {
            return $movies;
        }

        if (count($movieDivs) > 1) {
            $multipleFound = true;
        } else {
            $midLink = $htmlDom->dom()->find("#left_nav .section a", 0)->attr["href"];
            $mid = $this->getParamFromLink($midLink, "mid");
        }

        foreach ($movieDivs as $movieDiv) {
            $movie = new Movie();

            if ($multipleFound) {
                $movie->mid = $this->tryGetMid($movieDiv);
                $movie->name = $this->tryGetMovieTitle($movieDiv);
            } else {
                $movie->name = $this->tryGetMovieTitle($movieDiv);
                $movie->mid = $mid;
            }

            $infoDivs = $movieDiv->find("div.info");
            if (count($infoDivs) > 1) {
                $movie->info = join(" ", $infoDivs[1]->find("text"));
                $links = $movieDiv->find("div.links a");
                $movie->imdbLink = $this->getParamFromLink($links[count($links) - 1]->attr["href"], "q");
            } else {
                $movie->info = join(" ", $movieDiv->find("div.info", 0)->find("text"));
            }

            foreach ($movieDiv->find(".theater") as $theaterDiv) {

                $theaterHref = $theaterDiv->find(".name a", 0);

                $theater = new Theater();
                $theater->tid = $this->getParamFromLink($theaterHref->attr["href"], "tid");
                $theater->name = $theaterHref->innertext;
                $theater->address = strip_tags($theaterAddress = $theaterDiv->find(".address", 0)->innertext);

                foreach ($theaterDiv->find(".times") as $timeSpan) {
                    $texts = $timeSpan->find("text");

                    $showtime = new Showtime();
                    $showtime->info = $texts[0]->innertext;

                    foreach ($texts as $text) {
                        $time = trim(html_entity_decode($text->innertext));

                        preg_match("/^[0-9]{1,2}:[0-9]{1,2}/", $time, $matches);
                        if (count($matches) > 0) {
                            $showtime->times[] = $matches[0];
                        }
                    }

                    $theater->showtimes[] = $showtime;
                }

                $movie->theaters[] = $theater;
            }

            $movies[] = $movie;
        }

        return $movies;
    }

    /**
     * return the input identified by its name
     * @param $name
     * @param $htmlDom
     * @return null
     */
    private
    function getInputByName($name, $htmlDom)
    {
        foreach ($htmlDom->find("input[type=hidden]") as $input) {
            if ($input->attr["name"] == $name) {
                return $input->attribute["name"];
            }
        }

        return null;
    }

    /**
     * get value of a passes param in a query
     * @param $url
     * @param $paramName
     * @return null
     */
    private
    function getParamFromLink($url, $paramName)
    {
        $parts = parse_url(html_entity_decode($url));
        if (isset($parts)) {
            parse_str($parts['query'], $query);
            if (array_key_exists($paramName, $query)) {
                return $query[$paramName];
            }
        }

        return null;
    }

    /**
     * get the requested html from google with the passed parameters
     *
     * @param null $near
     * @param null $search
     * @param null $mid
     * @param null $tid
     * @param null $date
     * @param string $language
     * @return DataResponse
     */
    private
    function getData($near = null, $search = null, $mid = null, $tid = null, $date = null, $language = "de")
    {
        $params = array(
            'near' => $near,
            'mid' => $mid,
            'tid' => $tid,
            'q' => $search, //Movie title
            'hl' => $language, //en, de, fr...
            'date' => $date
        );

        $url = $this->_baseUrl . '?' . http_build_query($params);

        $client = new Client();
        $request = $client->createRequest("GET", $url);
        $request->setHeader('User-Agent', $this->_userAgent);

        $gresponse = $client->send($request);

        $response = new DataResponse();
        $response->body = $gresponse->getBody()->getContents();
        $response->code = $gresponse->getStatusCode();
        $response->headers = $gresponse->getHeaders();

        return $response;
    }

    /**
     * tries to find the mid for the Movie
     * @param $movieDiv
     * @return null
     * @throws Exception
     */
    private function tryGetMid(Crawler $movieDiv)
    {
        $mid = null;
        //first try => title link
        $titleHref = $movieDiv->filter(".header h2 a")->first();
        $href = $titleHref->attr("href");

        $mid = $this->getParamFromLink($href, "mid");

        if (!empty($mid)) {
            return $mid;
        }

        //second try => param on info links
        $links = $movieDiv->filter("div.links a")->first();
        foreach ($links as $link) {
            $mid = $this->getParamFromLink($link["href"], "mid");
            if (!empty($mid)) {
                return $mid;
            }
        }

        throw new \Exception("Can't find Movie ID (mid)!");
    }

    /**
     * tries to fetch the movie ID
     * @param $movieDiv
     * @return null|string
     * @throws \Exception
     */
    private
    function tryGetMovieTitle(Crawler $movieDiv)
    {
        $title = null;
        $header = $movieDiv->filter(".header h2 a")->first();
        if ($header != null && count($header) != 0) {
            $title = trim($header->text());
        } else {
            $header = $movieDiv->filter(".header h2")->first();
            if ($header != null && count($header) != 0) {
                $title = trim($header->text());
            }
        }

        if (!empty($title)) {
            return $title;
        }

        throw new \Exception("Can't find Movie Title!");
    }
}

?>