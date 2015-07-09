<?php

/**
 * This file is part of the Tmdb PHP API created by Michael Roterman.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Michael Roterman <michael@wtfz.net>
 * @copyright (c) 2013, Michael Roterman
 *
 * @version 0.0.1
 */

namespace GoogleMovieClient\HttpClient;

use GoogleMovieClient\Common\ParameterBag;
use GoogleMovieClient\Events\GoogleMovieClientEvents;
use GoogleMovieClient\Events\RequestEvent;
use GoogleMovieClient\Events\RequestSubscriber;
use GoogleMovieClient\HttpClient\Adapter\AdapterInterface;
use GoogleMovieClient\HttpClient\Adapter\GuzzleAdapter;
use GoogleMovieClient\HttpClient\Plugins\UserAgentHeaderPlugin;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Subscriber\Cache\CacheStorage;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HttpClient.
 */
class HttpClient
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    protected $options;

    /**
     * The base url to built requests on top of.
     *
     * @var null
     */
    protected $base_url = null;

    /**
     * @var Response
     */
    private $lastResponse;

    /**
     * @var Request
     */
    private $lastRequest;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(
        array $options = []
    ) {
        $this->options = $options;
        $this->base_url = $this->options['host'];
        $this->eventDispatcher = $this->options['event_dispatcher'];

        $this->setAdapter($this->options['adapter']);
        $this->processOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = [], array $headers = [])
    {
        return $this->send($path, 'GET', $parameters, $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $body, array $parameters = [], array $headers = [])
    {
        return $this->send($path, 'POST', $parameters, $headers, $body);
    }

    /**
     * {@inheritDoc}
     */
    public function head($path, array $parameters = [], array $headers = [])
    {
        return $this->send($path, 'HEAD', $parameters, $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $body = null, array $parameters = [], array $headers = [])
    {
        return $this->send($path, 'PUT', $parameters, $headers, $body);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, $body = null, array $parameters = [], array $headers = [])
    {
        return $this->send($path, 'PATCH', $parameters, $headers, $body);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $body = null, array $parameters = [], array $headers = [])
    {
        return $this->send($path, 'DELETE', $parameters, $headers, $body);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Get the current base url.
     *
     * @return null|string
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Set the base url secure / insecure.
     *
     * @param $url
     *
     * @return HttpClient
     */
    public function setBaseUrl($url)
    {
        $this->base_url = $url;

        return $this;
    }

    /**
     * Create the request object and send it out to listening events.
     *
     * @param       $path
     * @param       $method
     * @param array $parameters
     * @param array $headers
     * @param null  $body
     *
     * @return string
     */
    private function send($path, $method, array $parameters = [], array $headers = [], $body = null)
    {
        $request = $this->createRequest($path, $method, $parameters, $headers, $body);

        $event = new RequestEvent($request);
        $this->eventDispatcher->dispatch(GoogleMovieClientEvents::REQUEST, $event);

        $this->lastResponse = $event->getResponse();

        if ($this->lastResponse instanceof Response) {
            return (string) $this->lastResponse->getBody();
        }

        return [];
    }

    /**
     * Create the request object.
     *
     * @param       $path
     * @param       $method
     * @param array $parameters
     * @param array $headers
     * @param       $body
     *
     * @return Request
     */
    private function createRequest($path, $method, $parameters = [], $headers = [], $body)
    {
        $request = new Request();

        $request
            ->setPath($path)
            ->setMethod($method)
            ->setParameters(new ParameterBag((array) $parameters))
            ->setHeaders(new ParameterBag((array) $headers))
            ->setBody($body)
            ->setOptions(new ParameterBag((array) $this->options));

        return $this->lastRequest = $request;
    }

    /**
     * Add a subscriber.
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        if ($subscriber instanceof HttpClientEventSubscriber) {
            $subscriber->attachHttpClient($this);
        }

        $this->eventDispatcher->addSubscriber($subscriber);
    }

    /**
     * Remove a subscriber.
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        if ($subscriber instanceof HttpClientEventSubscriber) {
            $subscriber->attachHttpClient($this);
        }

        $this->eventDispatcher->removeSubscriber($subscriber);
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter
     *
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $adapter->registerSubscribers($this->getEventDispatcher());
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Register the default plugins.
     *
     * @return $this
     */
    public function registerDefaults()
    {
        $requestSubscriber = new RequestSubscriber();
        $this->addSubscriber($requestSubscriber);

        $userAgentHeaderPlugin = new UserAgentHeaderPlugin();
        $this->addSubscriber($userAgentHeaderPlugin);

        return $this;
    }

    public function isDefaultAdapter()
    {
        if (! class_exists('GuzzleHttp\Client')) {
            return false;
        }

        return ($this->getAdapter() instanceof GuzzleAdapter);
    }

    protected function processOptions()
    {
        $cache = $this->options['cache'];

        if ($cache['enabled']) {
            $this->setupCache($cache);
        }

        $log = $this->options['log'];

        if ($log['enabled']) {
            $this->setupLog($log);
        }
    }

    protected function setupCache(array $cache)
    {
        if ($this->isDefaultAdapter()) {
            $this->setDefaultCaching($cache);
        } elseif (null !== $subscriber = $cache['subscriber']) {
            $subscriber->setOptions($cache);
            $this->addSubscriber($subscriber);
        }
    }

    protected function setupLog(array $log)
    {
        if ($this->isDefaultAdapter()) {
            $this->setDefaultLogging($log);
        } elseif (null !== $subscriber = $log['subscriber']) {
            $subscriber->setOptions($log);
            $this->addSubscriber($subscriber);
        }
    }

    /**
     * Add an subscriber to enable caching.
     *
     * @param array $parameters
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function setDefaultCaching(array $parameters)
    {
        if (! class_exists('Doctrine\Common\Cache\CacheProvider')) {
            //@codeCoverageIgnoreStart
            throw new \RuntimeException(
                'Could not find the doctrine cache library,
                have you added doctrine-cache to your composer.json?'
            );
            //@codeCoverageIgnoreEnd
        }

        CacheSubscriber::attach(
            $this->getAdapter()->getClient(),
            ['storage' => new CacheStorage($parameters['handler'])]
        );

        return $this;
    }

    /**
     * Enable logging.
     *
     * @param array $parameters
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function setDefaultLogging(array $parameters)
    {
        if (! class_exists('\Monolog\Logger')) {
            //@codeCoverageIgnoreStart
            throw new \RuntimeException(
                'Could not find any logger set and the monolog logger library was not found
                to provide a default, you have to  set a custom logger on the client or
                have you forgot adding monolog to your composer.json?'
            );
            //@codeCoverageIgnoreEnd
        } else {
            $logger = new Logger('google-movie-client');
            $logger->pushHandler($parameters['handler']);
        }

        if ($this->getAdapter() instanceof GuzzleAdapter) {
            $subscriber = new LogSubscriber($logger);
            $this->getAdapter()->getClient()->getEmitter()->attach($subscriber);
        }

        return $this;
    }
}
