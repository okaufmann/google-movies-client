<?php
/**
 * This file is part of the Tmdb PHP API created by Michael Roterman.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Tmdb
 * @author Michael Roterman <michael@wtfz.net>
 * @copyright (c) 2013, Michael Roterman
 * @version 0.0.1
 */
namespace GoogleMovieClient\HttpClient\Adapter;

use GoogleMovieClient\Exceptions\HttpRequestException;
use GoogleMovieClient\HttpClient\Request;
use GoogleMovieClient\HttpClient\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface AdapterInterface
 * @package Tmdb\HttpClient
 */
interface AdapterInterface
{
    /**
     * Compose a GET request
     *
     * @throws HttpRequestException
     * @param  Request $request
     * @return Response
     */
    public function get(Request $request);

    /**
     * Send a HEAD request
     *
     * @throws HttpRequestException
     * @param  Request $request
     * @return Response
     */
    public function head(Request $request);

    /**
     * Compose a POST request
     *
     * @throws HttpRequestException
     * @param  Request $request
     * @return Response
     */
    public function post(Request $request);

    /**
     * Send a PUT request
     *
     * @throws HttpRequestException
     * @param  Request $request
     * @return Response
     */
    public function put(Request $request);

    /**
     * Send a DELETE request
     *
     * @throws HttpRequestException
     * @param  Request $request
     * @return Response
     */
    public function delete(Request $request);

    /**
     * Send a PATCH request
     *
     * @throws HttpRequestException
     * @param  Request $request
     * @return Response
     */
    public function patch(Request $request);

    /**
     * Return the used client
     *
     * @return mixed
     */
    public function getClient();

    /**
     * Register any specific subscribers
     *
     * @param  EventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function registerSubscribers(EventDispatcherInterface $eventDispatcher);
}
