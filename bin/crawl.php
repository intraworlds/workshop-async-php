#!/usr/bin/env php
<?php declare(strict_types=1);

$start = microtime(true);

if (empty($argv[1])) {
    die('Enter a type of crawler, eg. php ' . $argv[0] . ' <type-of-crawler> <url-to-crawl> [depth=3]');
}

if (empty($argv[2])) {
    die('Enter an URL, eg. php ' . $argv[0] . ' ' . $argv[1] . ' <url-to-crawl> [depth=3]');
}

$type = $argv[1];
$url = $argv[2];
$depth = (int)($argv[3] ?? 3);

require __DIR__ . '/../vendor/autoload.php';

switch ($type) {
    case 'sync':
        $urls = extract_urls_recursively($url, $depth, 'extract_urls');
        break;
    case 'unique':
        $urls = extract_urls_recursively($url, $depth, 'extract_unique_urls');
        break;
    case 'worker':
        predis()->del('visited');
        $urls = extract_urls_with_worker($url, $depth, uniqid());
        break;
    case 'promise':
        $urls = extract_urls_promise_recursively($url, $depth)->wait();
        break;
    default:
        die('Unknown type, use one of: sync, unique, worker, promise');
}


$count = 0;
foreach ($urls as $url) {
    $count++;
    printf("\033[33;1m#%d, %.1f c/s\033[0m %s" . PHP_EOL, $count, 1 / ((microtime(true) - $start) / $count), $url);
}

printf(PHP_EOL . 'Elapsed time: %.3fs, %d URLs found' . PHP_EOL, microtime(true) - $start, $count);
