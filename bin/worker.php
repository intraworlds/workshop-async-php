#!/usr/bin/env php
<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// vlastni implementace metody blpop, predis s ni nepracuje moc dobre (ignoruje
// dany timeout a spadne po case na globalnim)
function blpop(string $key): string {
    while (is_null($message = predis()->lpop($key))) {
        usleep(100000);
    }

    return $message;
}

while (true) {
    $message = blpop('queue');
    [$url, $depth, $results] = json_decode($message, true);

    echo 'Received: ' . $url . PHP_EOL;

    // nasledující řádek přidává nestabilitu workeru, při odkomentování je nutné
    // upravit kód tak, aby nedocházelo ke ztrátě dat
    // if (rand(0,1)) exit(1);

    foreach (extract_urls($url) as $u) {
        // napred zkus pridat hash URL do mnoziny, pokud je klic novy vrati 1, pokud v mnozine existuje
        // vrati 0, operace je atomicka, https://redis.io/commands/sadd
        if (!predis()->sadd($results . '_set', md5($u))) {
            continue;
        }

        predis()->rpush($results, json_encode($u));

        if ($depth > 0) {
            extract_urls_with_worker($u, $depth - 1, $results);
        }
    }
}
