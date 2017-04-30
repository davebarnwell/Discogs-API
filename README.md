# Discogs v2 API client

PHO client for the Discogs v2 API, currently with limited call support as was created as part
of a personal project, where I found the Discogs web search wasn't detailed enough but the ease
of adding and managing releases was great.

So This API client is mainly for exporting all releases, so they can be
put in a local MongoDb instance which provides better search.

```php
<?php
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
```
