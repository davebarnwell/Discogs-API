<?php
// php -S localhost:8000
/**
 * get All the items (albums) in the all collection and list as html
 */
$parentDir = dirname(__DIR__);
$rootDir   = dirname($parentDir);
require_once $rootDir . '/vendor/autoload.php';

$cache = new CacheSingleValue(__DIR__ . '/collection.cache', 3600 * 12);
$items = $cache->getCache();
if ($items == null) {
    $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($parentDir . '/config.yml'));

    $api = new davebarnwell\Discogs\APIClient($config['discogs-api-token']);
    $api->setUsername('dave.barnwell');

    $items    = [];
    $response = $api->getUsersCollectionAllReleases();
    $items    = array_merge($items, $response['releases']);
    while ($api->hasNextPage()) {
        $response = $api->getNextPage();
        $items    = array_merge($items, $response['releases']);
    }
    $cache->writeCache($items);
}
$formats = [];
foreach($items as $item) {
    $format = $item['basic_information']['formats'][0]['name'];
    $qty = $item['basic_information']['formats'][0]['qty'];
    if (!isset($formats[$format])) {
        $formats[$format] = 0;
    }
    $formats[$format] += $qty;
}

class CacheSingleValue
{
    private $cacheFilename;
    private $maxAgeSeconds;

    function __construct($cacheFilename, $maxAgeSeconds = 300)
    {
        $this->cacheFilename = $cacheFilename;
        $this->maxAgeSeconds = $maxAgeSeconds;
    }

    function writeCache($value)
    {
        file_put_contents($this->cacheFilename, serialize($value));
    }

    function getCache()
    {
        if ($this->isCachedExpired()) {
            return null;
        }
        $value = file_get_contents($this->cacheFilename);
        return unserialize($value);
    }

    function isCachedExpired()
    {
        if (!file_exists($this->cacheFilename)) {
            return true;
        }
        $modTime = filemtime($this->cacheFilename);
        return $modTime < (time() - $this->maxAgeSeconds);
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Album collection</title>

    <link rel="stylesheet" href="assets/css/main.css">
    <!--    <link rel="icon" href="images/favicon.png">-->
</head>

<body>
<section class="page_container">
    <h1>Discogs album collection</h1>
    <?php if ($items): ?>
        <div class="album-filters-container">
            <div>&nbsp;</div>
            <ul class="album-formats-nav">
                <li class="album-formats-nav__item album-formats-nav__item_selected" data-format="*">All (<?=number_format(count($items),0)?>)</li>
                <?php foreach($formats as $format => $qty): ?>
                    <li class="album-formats-nav__item" data-format="<?=htmlspecialchars(strtolower($format))?>"><?=htmlspecialchars($format)?> (<?=number_format($qty,0)?>)</li>
                <?php endforeach; ?>
            </ul>
            <div class="album-search__container">
                <input type="text" class="album-search__input">
            </div>
        </div>
        <ul class="album-list-container">
            <?php foreach ($items as $item): ?>
                <li class="album-list-instance"
                    data-format="<?= htmlspecialchars(strtolower($item['basic_information']['formats'][0]['name'])); ?>"
                    data-artist="<?= htmlspecialchars(strtolower($item['basic_information']['artists'][0]['name'])); ?>"
                    data-title="<?= htmlspecialchars(strtolower($item['basic_information']['title'])); ?>">
                    <div class="album-list-instance__image"><img src="<?= $item['basic_information']['thumb']; ?>"
                                                                 alt="<?= htmlspecialchars($item['basic_information']['title']); ?>;<?= htmlspecialchars($item['basic_information']['artists'][0]['name']); ?>">
                    </div>
                    <div class="album-list-instance__title"><?= htmlspecialchars($item['basic_information']['title']); ?></div>
                    <div class="album-list-instance__artist"><?= htmlspecialchars($item['basic_information']['artists'][0]['name']); ?></div>
                    </div>
                    <div class="album-list-instance__meta">
                        <div class="album-list-instance__format"><?= htmlspecialchars($item['basic_information']['formats'][0]['name']); ?></div>
                        <div class="album-list-instance__format-copies"><?= htmlspecialchars($item['basic_information']['formats'][0]['qty']); ?></div>
                        <div class="album-list-instance__year"><?= htmlspecialchars($item['basic_information']['year']); ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No items in your collection</p>
    <?php endif; ?>
</section>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
