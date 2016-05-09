<?php

namespace GoogleMoviesClient\Parsers;

use GoogleMoviesClient\Helpers\ParseHelper;
use GoogleMoviesClient\Models\Movie;
use GoogleMoviesClient\Models\ResultItem;
use GoogleMoviesClient\Models\Theater;
use Symfony\Component\DomCrawler\Crawler;

class ResultItemParser extends ParserAbstract
{
    /**
     * ResultItemParser constructor.
     *
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Parses the First Movie as Result Item.
     *
     * @param null $fallbackUrl
     *
     * @return Movie|null
     */
    public function parseResultMovieItem($fallbackUrl = null)
    {
        $resultItem = $this->parseResultItem($this->crawler, 'mid', '.info', $fallbackUrl);

        if ($resultItem == null) {
            return null;
        }

        $movie = new Movie($resultItem);

        return $movie;
    }

    /**
     * Parses the First Theater as Result Item.
     *
     * @param null $fallbackUrl
     *
     * @return Theater|null
     */
    public function parseResultTheaterItem($fallbackUrl = null)
    {
        $resultItem = $this->parseResultItem($this->crawler, 'tid', '.address, .info', $fallbackUrl);

        if ($resultItem == null) {
            return null;
        }

        $theater = new Theater($resultItem);

        return $theater;
    }

    /**
     * Parses the content as Result Item (Generic).
     *
     * @param Crawler $resultItemDiv
     * @param string  $paramName
     * @param string  $className
     * @param string  $fallbackUrl
     *
     * @return ResultItem|null
     */
    private function parseResultItem(
        Crawler $resultItemDiv,
        $paramName,
        $className,
        $fallbackUrl = null
    ) {
        $filter = 'h2[itemprop=name] a, h2[itemprop=name], .name a';

        $resultItemA = $resultItemDiv->filter($filter)->first();

        if ($resultItemA->count() > 0) {
            if ($resultItemA->nodeName() == "a") {
                $url = $resultItemA->attr('href');
            } elseif ($resultItemA->nodeName() == "h2" && $fallbackUrl != null) {
                $url = $fallbackUrl;
            }
        }else{
            throw new \Exception("Can't detect title of item!");
        }

        if (! $url) {
            return null;
        }

        $resultItem = new ResultItem();

        $resultItem->setId(ParseHelper::getParamFromLink($url, $paramName));
        $resultItem->setName($resultItemA->text());
        $resultItem->setInfo(strip_tags($resultItemDiv->filter($className)->first()->text()));

        return $resultItem;
    }
}
