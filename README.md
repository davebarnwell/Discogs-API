# Discogs v2 API client

This is a work in progress Discogs v2 API client, created as part of a personal project on Discogs
where I found the search wasn't detailed enough on Discogs but the ease of adding and managing
releases was great. So This API client is mainly for exporting all releases, so they can be
put in a document store which provides better search.


    # set your PSR-4 autoloader to namespace the src directory to contain davebarnwell\

    $yourApiToken = '';
    $yourUsername = '';

    $api = new davebarnwell\Discogs\APIClient($yourApiToken);
    $api->setUsername($yourUsername);
    
    $items    = [];
    $response = $api->getUsersCollectionAllReleases();
    $items    = array_merge($items, $response['releases']);
    while ($api->hasNextPage()) {
        $response = $api->getNextPage();
        $items    = array_merge($items, $response['releases']);
    }
    var_dump($items); # dump out all the music

