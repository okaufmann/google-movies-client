# Google Movie Client
[![StyleCI](https://styleci.io/repos/30374769/shield)](https://styleci.io/repos/30374769)
[![GitHub license](https://img.shields.io/github/license/strebl/pi.strebl.ch.svg?style=flat-square)](https://github.com/strebl/pi.strebl.ch/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/strebl/pi.strebl.ch.svg?style=flat-square)](https://github.com/strebl/pi.strebl.ch/releases)

Extended Client to fetch data from [http://google.com/movies](http://google.com/movies) in an object oriented way.

  - Search Movies Showtimes by Location and Movie Title
  - Get Showtimes of single Movie

## Dependencies

* [sunra/php-simple-html-dom-parser](https://github.com/sunra/php-simple-html-dom-parser) - PHP Dom Parser

### Installation

With [composer](https://getcomposer.org/)

```sh
$ composer require mighty-code/google-movies-client:dev-master
```

or

```json
"require": {
    "mighty-code/google-movies-client": "dev-master"
}
```
### Use it in your code

```php
use GoogleMovieClient\Client;

$client = new Client();
$result = $client->findShowtimesByMovieTitle("New York","American Sniper","en");
var_dump($result);
```

### Todo's

 - Parse and Include purchase Links where possible (movietickets.com)
 - Multipage Search Result parsing
 - Write Tests

License
----

MIT

Brought to you by [Mighty Code](http://mighty-code.com)

