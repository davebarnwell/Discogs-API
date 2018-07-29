#!/usr/bin/env php
<?php
/**
 * get All the items (albums) in the all collection
 */
$parentDir = dirname(__DIR__);
$rootDir   = dirname($parentDir);
require_once $rootDir . '/vendor/autoload.php';

$config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($parentDir . '/config.yml'));

$api = new davebarnwell\Discogs\APIClient($config['discogs-api-token']);
$api->setUsername('dave.barnwell');

$page = 0;
logGettingPage(++$page);
$items    = [];
$response = $api->getUsersCollectionAllReleases();
$items    = array_merge($items, $response['releases']);
while ($api->hasNextPage()) {
    logGettingPage(++$page);
    $response = $api->getNextPage();
    $items    = array_merge($items, $response['releases']);
}
echo json_encode($items, JSON_PRETTY_PRINT);

function logGettingPage($page) {
    fwrite(STDERR, "Getting page: $page\n");
    flush();
}