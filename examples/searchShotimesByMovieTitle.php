<?php

require_once '../vendor/autoload.php';

use GoogleMoviesClient\Client;

$test = new Client();
try {
    $days = $test->queryShowtimesByMovieNear('Captain America: Civil War', 'Interlaken', 'de');
    dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo '<hr/>';

    echo $ex->getTraceAsString();
}
