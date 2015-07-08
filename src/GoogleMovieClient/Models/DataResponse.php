<?php

namespace GoogleMovieClient\Models;

use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\DomCrawler\Crawler;

class DataResponse {
    protected $_dom = null;
    protected $_crawler = null;
    public $body;
    public $code;
    public $headers;

    public function dom()
    {
        if($this->_dom == null){
            $this->_dom = HtmlDomParser::str_get_html($this->body);
        }
        return $this->_dom;
    }

    public function getCrawler(){
        if($this->_crawler == null){
            $this->_crawler = new Crawler($this->body);
        }
        return $this->_crawler;
    }
}