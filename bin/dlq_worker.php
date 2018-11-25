#!/usr/bin/env php
<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// sa kolik sekund je zprava povazovana za nedorucenou a bude znovu zarazena do fronty
$timeout = 15;

while (true) {
    $dlq = new Predis\Collection\Iterator\ListKey(predis(), 'dlq');

    foreach ($dlq as $message) {
        [,,, $time] = json_decode($message, true);

        // zprava je v DLQ uz prilis dlouho, preposli ji
        if (($time + $timeout) < time()) {
            // vrat zpravu zpet do fronty
            predis()->lpush('queue', $message);

            // smaz ji z DLQ
            predis()->lrem('dlq', 1, $message);
        }
    }

    sleep(5);
}
