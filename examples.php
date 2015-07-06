<?php
require_once 'vendor/autoload.php';

use MightyCode\GoogleMovieClient\MovieClient;


$test = new MovieClient();
try {
    $days = $test->getShowtimesByMovieId("808c5c8cc99039b7", "Unterseen", "de");
    //$days = $test->findShowtimesByMovieTitle("Bern", "Minions", "de");
    //header('Content-Type: application/json; charset=utf-8');
    echo dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo "<hr/>";

    echo $ex->getTraceAsString();
}

