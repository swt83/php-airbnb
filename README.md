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

## Issues

Currently caps out at 300 homes.  Would require a polygon workaround to split up a region to search smaller parts before combinging them together.

## References

- [Python Script](https://github.com/nderkach/airbnb-python/tree/master/airbnb)
- [Apify Script](https://github.com/dtrungtin/actor-airbnb-scraper)