<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

$predis = new Predis\Client;

while (true) {
    if ($orderId = $predis->rpop('reserve_goods')) {
        $reserved = reserve_goods($orderId);

        if ($reserved) {
            $predis->lpush('notify_warehouse', $orderId);
            $predis->lpush('generate_invoice', $orderId);
        }
    } else {
        sleep(1);
    }
}
