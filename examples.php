<?php
require_once 'vendor/autoload.php';

use MightyCode\GoogleMovieClient\MovieClient;


$test = new MovieClient();
try {
    $days = $test->getShowtimesByTheaterId("eef3a3f57d224cf7", "Thun", "de");
    //$days = $test->getShowtimesByTheaterId("dac1dda012a03d0f", "Brooklyn", "en");
    //$days = $test->findShowtimesByMovieTitle("Bern", "Minions", "de");
    //header('Content-Type: application/json; charset=utf-8');
    echo dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo "<hr/>";

    echo $ex->getTraceAsString();
}

