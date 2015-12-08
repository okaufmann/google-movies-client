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
namespace GoogleMoviesClient\Events;

final class GoogleMovieClientEvents
{
    /** Request */
    const BEFORE_REQUEST = 'gmc.before_request';
    const REQUEST = 'gmc.request';
    const AFTER_REQUEST = 'gmc.after_request';
}
