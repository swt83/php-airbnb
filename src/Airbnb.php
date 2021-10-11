<?php

namespace Travis;

class Airbnb
{
    public static function search($apikey, $location)
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
                'location' => $location,
                #'limit' => 50,
                #'toddlers' => '0',
                #'adults' => '0',
                #'infants' => '0',
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
                #'timezone' => 'Europe/Lisbon',
                'satori_version' => '1.1.0'
            ]);

            // track pages
            $is_more = ex($results, 'explore_tabs.0.pagination_metadata.has_next_page') ? true : false;
            $page++;

            // get listings
            $listings = ex($results, 'explore_tabs.0.sections.0.listings');
            if (!$listings) $listings = ex($results, 'explore_tabs.0.sections.1.listings');
            if (!$listings) throw new \Exception('Unable to find listings.');

            // add to homes
            $homes = array_merge($homes, $listings);
        }

        // return
        return $homes;
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