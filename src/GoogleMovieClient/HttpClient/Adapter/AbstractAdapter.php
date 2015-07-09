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

/**
 * Interface AdapterInterface
 * @package Tmdb\HttpClient
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Create the unified exception to throw
     *
     * @param  Request  $request
     * @param  Response $response
     * @return HttpRequestException
     */
    protected function createApiException(Request $request, Response $response)
    {
        //$errors = json_decode((string) $response->getBody());

        return new HttpRequestException(
            $response->getCode(),
            $response->getBody(),
            $request,
            $response
        );
    }
}
