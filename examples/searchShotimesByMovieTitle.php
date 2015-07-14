<?php

require_once '../vendor/autoload.php';

use GoogleMoviesClient\Client;

$test = new Client();
try {
    $days = $test->queryShowtimesByMovieTitleNear('Bern', 'Minions', 'en');
    dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo '<hr/>';

    echo $ex->getTraceAsString();
}
