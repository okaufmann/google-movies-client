# Google Movie Client

Extended Client to fetch data from [http://google.com/movies](http://google.com/movies) in an object oriented way.

  - Search Movies Showtimes by Location and Movie Title
  - Get Showtimes of single Movie

## Dependencies

* [sunra/php-simple-html-dom-parser](https://github.com/sunra/php-simple-html-dom-parser) - PHP Dom Parser

### Installation

With [composer](https://getcomposer.org/)

```sh
$ composer require mighty-code/google-movie-client
```

```php
use MightyCode\GoogleMovieClient\Client;

$client = new Client();
$result = $client->findShowtimesByMovieTitle("New York","American Sniper","en");
var_dump($result);
```

### Todo's

 - Write Tests
 - Refactoring

License
----

MIT

Brought to you by [Mighty Code](http://mighty-code.com)