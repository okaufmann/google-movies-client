<?php

require_once 'vendor/autoload.php';

use GoogleMovieClient\Client;

$test = new Client();
try {
    //$days = $test->getShowtimesByTheaterId("eef3a3f57d224cf7", "Thun", "de");
    // $days = $test->getShowtimesByTheaterId("dac1dda012a03d0f", "Brooklyn", "en");
    //$days = $test->getTheatersNear("Bern", "en");
    $days = $test->queryShowtimesByMovieTitleNear('Bern', 'Minions', 'en');
    //header('Content-Type: application/json; charset=utf-8');
    dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo '<hr/>';

    echo $ex->getTraceAsString();
}
