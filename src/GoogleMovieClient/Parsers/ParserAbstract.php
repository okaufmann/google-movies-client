<?php

namespace GoogleMoviesClient\Parsers;

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
