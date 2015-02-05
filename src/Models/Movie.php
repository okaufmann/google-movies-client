<?php

namespace MightyCode\GoogleMovieClient\Models;

class Movie {
    public $mid;
    public $name;
    public $info;
    public $imdbLink;

    public $showtimeDays = [];
    public $theaters = [];
}