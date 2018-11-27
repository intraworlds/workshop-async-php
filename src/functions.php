<?php declare(strict_types=1);

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Promise\PromiseInterface;

// extrahuje odkazy ze zadané adresy a vrátí iterátor na ně
function extract_urls(string $url): Generator {
    if (($html = @file_get_contents($url)) === false) {
        return;
    }

    $crawler = new Crawler($html);

    $urls = [];
    foreach ($crawler->filter('a') as $node) {
        $url = $node->getAttribute('href');

        // ignoruj relativní adresy
        if (preg_match('/^(file|https?)/', $url)) {
            yield $url;
        }
    }
}

// extrahuje pouze unikátní URL ze zadané adresy
function extract_unique_urls(string $url): Generator {
    static $vidited = [];

    foreach (extract_urls($url) as $u) {
        if (!in_array($md5 = md5($u), $vidited)) {
            $vidited[] = $md5;
            yield $u;
        }
    }
}

// extrahuje odkazy ze zadané adresy a z odkazy z nich, atd. podle zadané hloubky
function extract_urls_recursively(string $url, int $depth, callable $crawler): Generator {
    foreach ($crawler($url) as $u) {
        yield $u;

        if ($depth > 0) {
            yield from extract_urls_recursively($u, $depth - 1, $crawler);
        }
    }
}

function predis(): Predis\Client {
    static $client;

    return $client ?? $client = new Predis\Client('tcp://redis');
}

// extrahuje pouze unikátní URL ze zadané adresy
function worker_extract_unique_urls(string $url): Generator {
    foreach (extract_urls($url) as $u) {
        if (predis()->hsetnx('visited', md5($u), 1)) {
            yield $u;
        }
    }
}

// přidá zadanou adresu do fronty pro zpracování workerem
function extract_urls_with_worker(string $url, int $depth, string $results): Generator {
    predis()->rpush('queue', json_encode([$url, $depth, $results]));

    // generátor, který poslouchá na frontě výsledků, pokud nebude zavolaný, kód se neprovede
    return (function () use ($results) {
        while (true) {
            [, $message] = predis()->blpop($results, 5);
            if ($message) {
                yield json_decode($message);
            } else {
                break;
            }
        }
    })();
}

function guzzle(): GuzzleHttp\Client {
    static $client;

    return $client ?? $client = new GuzzleHttp\Client;
}

// stahuje zadané odkazy pomocí neblokující funkce z Guzzle knihovny, vrací Promise na pole odkazů
function extract_urls_promise(string $url): PromiseInterface {
    static $count = 1;
    return guzzle()->getAsync($url, ['http_errors' => false])
        ->then(function ($response) use (&$count) {
            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $crawler = new Crawler($response->getBody()->getContents());

            $urls = [];
            foreach ($crawler->filter('a') as $node) {
                $url = $node->getAttribute('href');

                if (preg_match('/^(file|https?)/', $url)) {
                    $urls[] = $url;
                    echo $url . "\r";
                }
            }

            return $urls;
        });
}

function extract_urls_promise_recursively(string $url, int $depth): PromiseInterface {
    return extract_urls_promise($url)
        ->then(function ($urls) use ($depth) {
            $promises = [GuzzleHttp\Promise\promise_for($urls)];

            if ($depth > 0) {
                foreach ($urls as $u) {
                    $promises[] = extract_urls_promise_recursively($u, $depth - 1);
                }
            }

            // spoj výsledky s předchozích volání
            return GuzzleHttp\Promise\all($promises)
                ->then(function ($sets) {
                    return array_merge(...$sets);
                });
        });
}
