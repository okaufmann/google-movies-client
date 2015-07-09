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

namespace GoogleMovieClient\HttpClient\Plugins;

use GoogleMovieClient\Events\GoogleMovieClientEvents;
use GoogleMovieClient\Events\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AcceptJsonHeaderPlugin.
 */
class UserAgentHeaderPlugin implements EventSubscriberInterface
{
    //current release of chrome. Got user agent string from: http://www.useragentstring.com/pages/Chrome/
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';

    public static function getSubscribedEvents()
    {
        return [
            GoogleMovieClientEvents::BEFORE_REQUEST => 'onBeforeSend',
        ];
    }

    public function onBeforeSend(RequestEvent $event)
    {
        $event->getRequest()->getHeaders()->set(
            'User-Agent',
            self::USER_AGENT
        );
    }
}
