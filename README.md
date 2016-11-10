# Google Movie Client  [abandoned]
[![StyleCI](https://styleci.io/repos/30374769/shield)](https://styleci.io/repos/30374769)
[![GitHub license](https://img.shields.io/github/license/okaufmann/google-movies-client.svg?style=flat-square)](https://github.com/okaufmann/google-movies-client/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/okaufmann/google-movies-client.svg?style=flat-square)](https://github.com/okaufmann/google-movies-client/releases)

**Unfortunately Google quit its Service by the 1th of November, so this project is useless.**

Extended Client to fetch data from [http://google.com/movies](http://google.com/movies) in an object oriented way.

  - Search Movies Showtimes by Location and Movie Title
  - Get Showtimes of single Movie
  - Get Showtimes of single Theater
  - Get Theaters near a location
  - Get Movies near a location

### Installation

With [composer](https://getcomposer.org/)

```sh
$ composer require okaufmann/google-movies-client:dev-master
```

or

```json
"require": {
    "okaufmann/google-movies-client": "dev-master"
}
```
### Use it in your code

```php
use GoogleMoviesClient\Client;

$client = new Client();
$result = $client->queryShowtimesByMovieNear("Minions", "New York", "en");
dd($result);
```

**NOTE:**: You have to give the `$near` parameter on every call. Otherwise you will get strange wrong information.

### Todo's

 - Parse and Include purchase Links where possible (movietickets.com)
 - Multipage Search Result parsing
 - Write Tests

License
----

MIT

