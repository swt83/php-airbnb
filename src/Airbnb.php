<?php

namespace Travis;

use Travis\CLI;
use Travis\Nominatim;

class Airbnb
{
    public static function search($apikey, $location)
    {
        // init
        $homes = [];

        // get grids
        CLI::write('Generating grid of "'.$location.'"...');
        $grids = static::grids($location);
        $total = sizeof($grids);
        CLI::write($total.' grids found...');

        // foreach grid...
        foreach ($grids as $num => $grid)
        {
            CLI::progress($num, $total);

            // query for homes
            $results = static::query($apikey, $grid);

            // merge
            $homes = array_merge($homes, $results);
        }
        CLI::progress_complete();

        // dedupe
        $history = [];
        $dupes = [];
        foreach ($homes as $key => $home)
        {
            $list_id = ex($home, 'listing.id');

            if (isset($history[$list_id]))
            {
                $dupes[$list_id] = 1;
                unset($homes['key']);
            }

            $history[$list_id] = 1;
        }

        // report
        CLI::write(number_format(sizeof($dupes)).' dupes found.');
        CLI::write(number_format(sizeof($homes)).' homes found.');

        // return
        return $homes;
    }

    protected static function query($apikey, $grid)
    {
        // init
        $homes = [];

        // track pages
        $is_more = true;
        $page = 1;
        $size = 50; // max allowed

        // while there is more...
        while ($is_more)
        {
            $results = static::request('https://www.airbnb.com/api/v2/explore_tabs/', [
                'key' => $apikey,
                #'location' => $location,
                'search_by_map' => 'true',
                'ne_lat' => ex($grid, 'ne_lat'),
                'ne_lng' => ex($grid, 'ne_lon'),
                'sw_lat' => ex($grid, 'sw_lat'),
                'sw_lng' => ex($grid, 'sw_lon'),
                'is_guided_search' => 'true',
                'version' => '1.4.8',
                'section_offset' => 0,
                'items_offset' => ($page - 1) * $size,
                'items_per_grid' => $size,
                'screen_size' => 'small',
                'source' => 'explore_tabs',
                '_format' => 'for_explore_search_native',
                'metadata_only' => 'false',
                'refinement_paths[]' => '/homes',
                'satori_version' => '1.1.0'
                #'timezone' => 'Europe/Lisbon',
                #'toddlers' => '0',
                #'adults' => '0',
                #'infants' => '0',
            ]);

            // track pages
            $is_more = ex($results, 'explore_tabs.0.pagination_metadata.has_next_page') ? true : false;
            $page++;

            // get listings
            $listings = [];
            foreach (ex($results, 'explore_tabs.0.sections') as $section)
            {
                $listings = array_merge($listings, ex($section, 'listings', []));
            }

            // add to homes
            $homes = array_merge($homes, $listings);
        }

        // check for overload
        if (sizeof($homes) >= 300)
        {
            throw new \Exception('More than 300 homes in grid!');
        }

        // return
        return $homes;
    }

    protected static function grids($location, $radius = 1)
    {
        // get bounding box of city
        $city = Nominatim::to_coords($location);

        // get grid of bboxes
        return Nominatim::calc_point_grid(ex($city, '0.boundingbox.1'), ex($city, '0.boundingbox.3'), ex($city, '0.boundingbox.0'), ex($city, '0.boundingbox.2'), 'km', $radius);
    }

    protected static function request($endpoint, $arguments, $timeout = 30)
    {
        // set url
        $url = $endpoint;

        // build query
        $url .= '?';
        foreach($arguments as $key => $value)
        {
            $url .= $key.'='.urlencode($value).'&';
        }

        // make request
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ];
        $context = stream_context_create($opts);
        $content = file_get_contents($url, false, $context);

        // decode
        return json_decode($content);
    }
}