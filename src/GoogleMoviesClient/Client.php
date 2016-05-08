<?php

namespace GoogleMoviesClient;

use Carbon\Carbon;
use Doctrine\Common\Cache\FilesystemCache;
use GoogleMoviesClient\Helpers\ParseHelper;
use GoogleMoviesClient\HttpClient\Adapter\AdapterInterface;
use GoogleMoviesClient\HttpClient\Adapter\GuzzleAdapter;
use GoogleMoviesClient\HttpClient\HttpClient;
use GoogleMoviesClient\Models\DataResponse;
use GoogleMoviesClient\Parsers\ShowtimeParser;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Client implements ClientInterface
{
    const GOOGLE_MOVIE_URL = 'www.google.com/movies';

    /**
     * Stores the HTTP Client.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Store the options.
     *
     * @var array
     */
    private $options = [];

    /**
     * Construct our client.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->configureOptions($options);
        $this->constructHttpClient();
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Get the adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->options['adapter'];
    }

    /**
     * Get the event dispatcher.
     *
     * @return AdapterInterface
     */
    public function getEventDispatcher()
    {
        return $this->options['event_dispatcher'];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getOption($key)
    {
        return array_key_exists($key, $this->options) ? $this->options : null;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function setOptions(array $options = [])
    {
        $this->options = $this->configureOptions($options);
    }

    /**
     *  Returns Showtimes for a specific movie near a location.
     *
     * @param string $mid
     * @param string $nearLocation
     * @param string $lang
     *
     * @return array
     */
    public function getShowtimesByMovieId($mid, $nearLocation, $lang = 'en', $dateOffset = 0)
    {
        //http://google.com/movies?near=thun&hl=de&mid=808c5c8cc99039b7

        //TODO: Add multiple result pages parsing

        $dataResponse = $this->getData($nearLocation, null, $mid, null, $lang, $dateOffset);
        $days = [];
        if ($dataResponse) {
            $dayDate = Carbon::now();
            $dayDate->setTime(0, 0, 0);

            $parser = new ShowtimeParser($dataResponse->getCrawler());
            $result = $parser->getShowtimeDayByMovie($dayDate->copy());
            $days[] = $result;

            for ($i = $dateOffset + 1; $i < 20; $i++) {
                $dataResponse = $this->getData($nearLocation, null, $mid, null, $lang, $i);

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
     * Returns Showtimes for a specific Theater.
     *
     * @param string $tid
     * @param        $nearLocation
     * @param string $lang
     * @param int    $dateOffset
     *
     * @return mixed
     */
    public function getShowtimesByTheaterId($tid, $nearLocation, $lang = 'en', $dateOffset = 0)
    {
        //http://google.com/movies?tid=eef3a3f57d224cf7&hl=de

        //TODO: Add multiple result pages parsing

        $dataResponse = $this->getData($nearLocation, null, null, $tid, $lang, $dateOffset);
        $days = [];
        if ($dataResponse) {
            $dayDate = Carbon::now();
            $dayDate->setTime(0, 0, 0);

            $parser = new ShowtimeParser($dataResponse->getCrawler());

            $result = $parser->getShowtimeDayByTheater($dayDate->copy());
            $days[] = $result;

            for ($i = $dateOffset + 1; $i < 20; $i++) {
                $dataResponse = $this->getData($nearLocation, null, null, $tid, $lang, $i);

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
     * Returns Theaters near a location.
     *
     * @param string $nearLocation
     * @param string $lang
     *
     * @return mixed
     */
    public function getTheatersNear($nearLocation, $lang = 'en')
    {
        //http://google.com/movies?near=thun&hl=de
        //http://google.com/movies?near=thun&hl=de&start=10 (next page)

        $dataResponse = $this->getData($nearLocation, null, null, null, $lang);
        $theaters = [];

        if ($dataResponse) {
            $crawler = $dataResponse->getCrawler();
            $parser = new ShowtimeParser($crawler);
            $theaters = $parser->parseTheaters(false);

            $navBarPageLinks = $crawler->filter('#navbar a');
            $furtherPages = $navBarPageLinks->each(function (Crawler $node, $i) {
                return $temp = ParseHelper::getParamFromLink($node->attr('href'), 'start');
            });
            $furtherPages = array_unique($furtherPages);

            foreach ($furtherPages as $page) {
                $dataResponse = $this->getData($nearLocation, null, null, null, $lang, null, $page);
                if ($dataResponse) {
                    $parser = new ShowtimeParser($dataResponse->getCrawler());
                    $theaters = array_merge($parser->parseTheaters(false), $theaters);
                }
            }
        }

        return $theaters;
    }

    /**
     * Returns Showtimes found by a search for a movie title.
     *
     * @param string $nearLocation
     * @param string $name
     * @param string $lang
     * @param null   $dateOffset
     *
     * @throws \Exception
     *
     * @return mixed|null
     */
    public function queryShowtimesByMovieNear($movieTitle, $nearLocation, $lang = 'en', $dateOffset = null)
    {
        // http://google.com/movies?near=Thun&hl=de&q=jurassic+world

        $dataResponse = $this->getData($nearLocation, $movieTitle, null, null, $lang);
        $days = [];
        $movie = null;

        if ($dataResponse) {
            $dayDate = Carbon::now();
            $dayDate->setTime(0, 0, 0);
            $crawler = $dataResponse->getCrawler();
            $parser = new ShowtimeParser($crawler);
            $movies = $parser->parseMovies(false);

            //TODO: Replace by handeles multiple movies in results!

            $movie = $movies[0];

            if (count($movies) > 1) {
                throw new \Exception('more than one movie in search results are not supported yet!');
            } else {
                //Dirty but didn't found better way...
                $midHref = $crawler->filter('#left_nav a')->first()->attr('href');
                $movie->setMid(ParseHelper::getParamFromLink($midHref, 'mid'));
            }

            $days[] = $parser->getShowtimeDayByMovie($dayDate->copy());

            for ($i = $dateOffset + 1; $i < 20; $i++) {
                $dataResponse = $this->getData($nearLocation, $movieTitle, null, null, $lang, $i);

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
     * Returns Showtimes near a location.
     *
     * @param string $nearLocation
     * @param string $lang
     * @param null   $dateOffset
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getMoviesNear($nearLocation, $lang = 'en', $dateOffset = null)
    {
        //http://google.com/movies?near=New%20York&hl=en&sort=1
        //http://google.com/movies?near=New%20York&hl=en&sort=1&start=10

        $dataResponse = $this->getData($nearLocation, null, null, null, $lang, null, null, 1);

        $movies = [];

        if ($dataResponse) {
            $crawler = $dataResponse->getCrawler();
            $parser = new ShowtimeParser($crawler);
            $movies = $parser->parseMovies(false);

            $navBarPageLinks = $crawler->filter('#navbar a');
            $furtherPages = $navBarPageLinks->each(function (Crawler $node, $i) {
                return $temp = ParseHelper::getParamFromLink($node->attr('href'), 'start');
            });

            $furtherPages = array_unique($furtherPages);

            foreach ($furtherPages as $page) {
                $dataResponse = $this->getData($nearLocation, null, null, null, $lang, null, $page, 1);
                if ($dataResponse) {
                    $parser = new ShowtimeParser($dataResponse->getCrawler());
                    $pageMovies = $parser->parseMovies(false);
                    $movies = array_merge($movies, $pageMovies);
                }
            }
        }

        return $movies;
    }

    /**
     * Construct the http client.
     *
     * @return void
     */
    protected function constructHttpClient()
    {
        $hasHttpClient = (null !== $this->httpClient);
        $this->httpClient = new HttpClient($this->getOptions());
        if (! $hasHttpClient) {
            $this->httpClient->registerDefaults();
        }
    }

    /**
     * Configure options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function configureOptions(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'adapter'          => null,
            'secure'           => true,
            'host'             => self::GOOGLE_MOVIE_URL,
            'base_url'         => null,
            'token'            => null,
            'session_token'    => null,
            'event_dispatcher' => array_key_exists('event_dispatcher',
                $this->options) ? $this->options['event_dispatcher'] : new EventDispatcher(),
            'cache'            => [],
            'log'              => [],
        ]);
        $resolver->setRequired([
            'adapter',
            'host',
            'token',
            'secure',
            'event_dispatcher',
            'cache',
            'log',
        ]);
        $resolver->setAllowedTypes('adapter', ['object', 'null']);
        $resolver->setAllowedTypes('host', ['string']);
        $resolver->setAllowedTypes('event_dispatcher', ['object']);
        $this->options = $resolver->resolve($options);
        $this->postResolve($options);

        return $this->options;
    }

    /**
     * Configure caching.
     *
     * @param array $options
     *
     * @return array
     */
    protected function configureCacheOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'enabled'    => true,
            'handler'    => null,
            'subscriber' => null,
            'path'       => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'google-movie-client',
        ]);
        $resolver->setRequired([
            'enabled',
            'handler',
        ]);
        $resolver->setAllowedTypes('enabled', ['bool']);
        $resolver->setAllowedTypes('handler', ['object', 'null']);
        $resolver->setAllowedTypes('subscriber', ['object', 'null']);
        $resolver->setAllowedTypes('path', ['string', 'null']);
        $options = $resolver->resolve(array_key_exists('cache', $options) ? $options['cache'] : []);
        if ($options['enabled'] && ! $options['handler']) {
            $options['handler'] = new FilesystemCache(
                $options['path']
            );
        }

        return $options;
    }

    /**
     * Configure logging.
     *
     * @param array $options
     *
     * @return array
     */
    protected function configureLogOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'enabled'    => false,
            'level'      => LogLevel::DEBUG,
            'handler'    => null,
            'subscriber' => null,
            'path'       => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'google-movie-client.log',
        ]);
        $resolver->setRequired([
            'enabled',
            'level',
            'handler',
        ]);
        $resolver->setAllowedTypes('enabled', ['bool']);
        $resolver->setAllowedTypes('level', ['string']);
        $resolver->setAllowedTypes('handler', ['object', 'null']);
        $resolver->setAllowedTypes('path', ['string', 'null']);
        $resolver->setAllowedTypes('subscriber', ['object', 'null']);
        $options = $resolver->resolve(array_key_exists('log', $options) ? $options['log'] : []);
        if ($options['enabled'] && ! $options['handler']) {
            $options['handler'] = new StreamHandler(
                $options['path'],
                $options['level']
            );
        }

        return $options;
    }

    /**
     * Post resolve.
     *
     * @param array $options
     */
    protected function postResolve(array $options = [])
    {
        $this->options['base_url'] = sprintf(
            '%s://%s',
            'https',
            $this->options['host']
        );
        if (! $this->options['adapter']) {
            $this->options['adapter'] = new GuzzleAdapter(
                new \GuzzleHttp\Client(['base_url' => $this->options['base_url']])
            );
        }
        $this->options['cache'] = $this->configureCacheOptions($options);
        $this->options['log'] = $this->configureLogOptions($options);
    }

    /**
     * get the requested html from google with the passed parameters.
     *
     * @param null   $nearLocation
     * @param null   $search
     * @param null   $mid
     * @param null   $tid
     * @param string $language
     * @param null   $date
     * @param int    $start
     *
     * @return DataResponse
     */
    private function getData(
        $near = null,
        $search = null,
        $mid = null,
        $tid = null,
        $language = 'en',
        $date = null,
        $start = null,
        $sort = null
    ) {
        $params = [
            'near'  => $nearLocation,
            'mid'   => $mid,
            'tid'   => $tid,
            'q'     => $search, //Movie title
            'hl'    => $language, //en, de, fr...
            'date'  => $date,
            'start' => $start,
            'sort'  => $sort,
        ];

        $url = '?' . http_build_query($params);

        $guzzle_response = $this->httpClient->get($url);

        $response = new DataResponse();
        $response->body = $guzzle_response;

        return $response;
    }
}
