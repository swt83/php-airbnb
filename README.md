# Airbnb

A PHP library for working w/ the Airbnb API.

## Install

Normal install via Composer.

## Usage

This uses the "unofficial" API to search listings on Airbnb.  To find your API key, go to [airbnb.com](https://www.airbnb.com), open developer tools, click network, and search for ``key``.

```php
use Travis\Airbnb;

$apikey = 'YOURAPIKEY';
$listings = Airbnb::search($apikey, 'Austin, TX'); // returns array
```

Available documentation is unfortunately very limited, and I found the most useful tips searching various [Python](https://github.com/nderkach/airbnb-python/tree/master/airbnb) libraries on Github to see how they worked.

Note that these searches can take a long time and it's probably more appropriate for use from the command line.

## Challenges

Airbnb limits search results to 300 records.  This library uses [code](https://github.com/swt83/php-nominatim) to split the location into 1 km grids, and combines the search results from each.

## References

- [Python Script](https://github.com/nderkach/airbnb-python/tree/master/airbnb)
- [Apify Script](https://github.com/dtrungtin/actor-airbnb-scraper)