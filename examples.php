<?php
require_once 'vendor/autoload.php';

use MightyCode\GoogleMovieClient\MovieClient;


$test = new MovieClient();
try {
    $days = $test->getShowtimesByMovieId("New York", "808c5c8cc99039b7");
    //$days = $test->findShowtimesByMovieTitle("Bern", "Minions", "de");
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo "<hr/>";

    echo $ex->getTraceAsString();
}

