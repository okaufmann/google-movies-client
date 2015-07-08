<?php
/**
 * Created by PhpStorm.
 * User: okaufmann
 * Date: 07.07.2015
 * Time: 07:08
 */

namespace GoogleMovieClient\Parsers;

use Symfony\Component\DomCrawler\Crawler;

abstract class ParserAbstract
{
    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @return Crawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * @param Crawler $crawler
     */
    public function setCrawler($crawler)
    {
        $this->crawler = $crawler;
    }
}
