# AirBNB

A PHP library for working w/ the AirBNB API.

## Install

Normal install via Composer.

## Usage

```php
use Travis\AirBNB;

// get geo info on an address
$lookup = ArcGIS::geocode('777 Pearly Gates, Austin, TX');
$longitude = ex($lookup, 'candidates.0.location.x');
$latitude = ex($lookup, 'candidates.0.location.y');

// use a hosted ArcGIS resource to query additional data for those XY coords
$endpoint = 'https://maps.cityoffbg.com/arcgis/rest/services/LandUse/Zoning/MapServer/1/'; //ending slash is important
$lookup = ArcGIS::query($endpoint, $longitude, $latitude);

// The data returned depends on what fields are available by the resource being queried.
```

See the [API Guide](https://stevesie.com/apps/airbnb-api/listings) for additional methods.