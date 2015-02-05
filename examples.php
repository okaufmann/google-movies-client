<?php


require_once 'vendor/autoload.php';

use MightyCode\GoogleMovieClient\Client;

$test = new Client();

$days = $test->getShowtimesByMovieId("New York", "1b9bce36e4cc7c72");
//$days = $test->findShowtimesByMovieTitle("New York","American Sniper","en");

var_dump($days);

?>
