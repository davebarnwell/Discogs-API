<?php
/**
 * get All the items (albums) in the all collection
 */
require_once 'vendor/autoload.php';

$config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

$api = new davebarnwell\Discogs\APIClient($config['discogs-api-token']);
$api->setUsername('dave.barnwell');

$items    = [];
$response = $api->getUsersCollectionAllReleases();
$items    = array_merge($items, $response['releases']);
while ($api->hasNextPage()) {
    $response = $api->getNextPage();
    $items    = array_merge($items, $response['releases']);
}
var_dump($items);