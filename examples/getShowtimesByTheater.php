<?php

require_once '../vendor/autoload.php';

use GoogleMovieClient\Client;

$test = new Client();
try {
    $days = $test->getShowtimesByTheaterId('eef3a3f57d224cf7', 'Thun', 'de');
    dd($days);
} catch (Exception $ex) {
    echo $ex->getMessage();

    echo '<hr/>';

    echo $ex->getTraceAsString();
}
