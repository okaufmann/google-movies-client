<?php

namespace MightyCode\GoogleMovieClient;

use GuzzleHttp\Client;
use MightyCode\GoogleMovieClient\Models\DataResponse;

class MovieClient implements \MovieClientInterface
{

    private $_baseUrl = "http://www.google.com/movies";

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

        $dataResponse = $this->getData($near, null, $mid, null, $date, $lang);
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
    private function getData($near = null, $search = null, $mid = null, $tid = null, $language = "en", $date = null, $start = 0)
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
}