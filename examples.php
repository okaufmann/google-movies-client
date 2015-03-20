<?php


require_once 'vendor/autoload.php';

use MightyCode\GoogleMovieClient\MovieClient;

$test = new MovieClient();

//$days = $test->getShowtimesByMovieId("New York", "1b9bce36e4cc7c72");
$days = $test->findShowtimesByMovieTitle("Thun","Kingsman","de");

var_dump($days);

?>
