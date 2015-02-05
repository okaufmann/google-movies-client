<?php

namespace MightyCode\GoogleMovieClient\Models;

use Sunra\PhpSimple\HtmlDomParser;

class DataResponse {
    protected $_dom = null;
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
}