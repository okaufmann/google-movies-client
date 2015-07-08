<?php
/**
 * Created by PhpStorm.
 * User: okaufmann
 * Date: 07.07.2015
 * Time: 07:06
 */

namespace GoogleMovieClient\Parsers;


use GoogleMovieClient\Helpers\ParseHelper;
use GoogleMovieClient\Models\Movie;
use GoogleMovieClient\Models\ResultItem;
use GoogleMovieClient\Models\Theater;
use Symfony\Component\DomCrawler\Crawler;

class ResultItemParser extends ParserAbstract
{
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function parseResultMovieItem()
    {
        $resultItem = $this->parseResultItem($this->crawler, 'mid', '.info');

        if ($resultItem == null) {
            return null;
        }

        $movie = new Movie($resultItem);

        return $movie;
    }

    public function parseResultTheaterItem()
    {
        $resultItem = $this->parseResultItem($this->crawler, 'tid', '.address, .info');

        if ($resultItem == null) {
            return null;
        }

        $theater = new Theater($resultItem);

        return $theater;
    }

    /**
     * @param Crawler $resultItemDiv
     * @param $paramName
     * @param $className
     * @return ResultItem|null
     */
    private function parseResultItem(Crawler $resultItemDiv, $paramName, $className)
    {
        $resultItemA = $resultItemDiv->filter("h2 a, .name a")->first();

        $url = $resultItemA->attr("href");

        if (!$url) {
            return null;
        }

        $resultItem = new ResultItem();

        $resultItem->setId(ParseHelper::getParamFromLink($url, $paramName));
        $resultItem->setName($resultItemA->text());
        $resultItem->setInfo(strip_tags($resultItemDiv->filter($className)->first()->text()));

        return $resultItem;
    }
}