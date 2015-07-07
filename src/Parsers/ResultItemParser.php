<?php
/**
 * Created by PhpStorm.
 * User: okaufmann
 * Date: 07.07.2015
 * Time: 07:06
 */

namespace MightyCode\GoogleMovieClient\Parsers;


use MightyCode\GoogleMovieClient\Helpers\ParseHelper;
use MightyCode\GoogleMovieClient\Models\Movie;
use MightyCode\GoogleMovieClient\Models\ResultItem;
use MightyCode\GoogleMovieClient\Models\Theater;
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
        $resultItem = $this->parseResultItem($this->crawler, 'tid', '.address');

        if ($resultItem == null) {
            return null;
        }

        $theater = new Theater($resultItem);

        return $theater;
    }

    /**
     * @param Crawler $theaterDiv
     * @param $paramName
     * @param $className
     * @return ResultItem|null
     */
    private function parseResultItem(Crawler $theaterDiv, $paramName, $className)
    {
        $theaterHref = $theaterDiv->filter(".name a")->first();

        $url = $theaterHref->attr("href");

        if (!$url) {
            return null;
        }

        $resultItem = new ResultItem();

        $resultItem->setId(ParseHelper::getParamFromLink($url, $paramName));
        $resultItem->setName($theaterHref->text());
        $resultItem->setInfo(strip_tags($theaterDiv->filter($className)->first()->text()));

        return $resultItem;
    }
}