<?php

namespace GoogleMoviesClient\Helpers;

class ParseHelper
{
    /**
     * Get value of a passes param in a query.
     *
     * @param $url
     * @param $paramName
     *
     * @return null
     */
    public static function getParamFromLink($url, $paramName)
    {
        if (!$url) {
            throw new \InvalidArgumentException('url');
        }

        if (!$paramName) {
            throw new \InvalidArgumentException('paramName');
        }

        $parts = parse_url(html_entity_decode($url));
        if (isset($parts)) {
            parse_str($parts['query'], $query);
            if (array_key_exists($paramName, $query)) {
                return $query[$paramName];
            }
        }

        return null;
    }
}
