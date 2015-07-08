# Google Movie Client
[![StyleCI](https://styleci.io/repos/30374769/shield)](https://styleci.io/repos/30374769)
[![GitHub license](https://img.shields.io/github/license/okaufmann/google-movie-client.svg?style=flat-square)](https://github.com/okaufmann/google-movie-client/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/okaufmann/google-movie-client.svg?style=flat-square)](https://github.com/okaufmann/google-movie-client/releases)

Extended Client to fetch data from [http://google.com/movies](http://google.com/movies) in an object oriented way.

  - Search Movies Showtimes by Location and Movie Title
  - Get Showtimes of single Movie
  - Get Showtimes of single Theater
  - Get Theaters near a location

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
$result = $client->queryShowtimesByMovieTitleNear("New York","American Sniper","en");
dd($result);
```

### Todo's

 - Parse and Include purchase Links where possible (movietickets.com)
 - Multipage Search Result parsing
 - Write Tests

License
----

MIT

