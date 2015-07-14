<?php

require_once '../vendor/autoload.php';

use GoogleMoviesClient\Client;

$test = new Client();
try {
    $days = $test->getShowtimesByMovieId('808c5c8cc99039b7', 'Thun', 'de');
    dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo '<hr/>';

    echo $ex->getTraceAsString();
}
