<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

$predis = new Predis\Client;

while (true) {
    if ($orderId = $predis->rpop('notify_warehouse')) {
        notify_warehouse($orderId);
    } else {
        sleep(1);
    }
}
