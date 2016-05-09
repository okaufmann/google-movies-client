<?php

require_once '../vendor/autoload.php';

use GoogleMoviesClient\Client;

$test = new Client();
try {
    // captain america: civil war
    $days = $test->getShowtimesByMovieId('d86e1329eefc10e5', 'Thun', 'de');
    dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo '<hr/>';

    echo $ex->getTraceAsString();
}
