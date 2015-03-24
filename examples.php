<?php
require_once 'vendor/autoload.php';

use MightyCode\GoogleMovieClient\MovieClient;


$test = new MovieClient();
try {
    //$days = $test->getShowtimesByMovieId("New York", "1b9bce36e4cc7c72");
    $days = $test->findShowtimesByMovieTitle("Bern", "Kingsman", "de");
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo "<hr/>";

    echo $ex->getTraceAsString();
}

