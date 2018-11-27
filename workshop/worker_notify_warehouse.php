<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

$predis = new Predis\Client;

while (true) {
    if ($orderId = $predis->rpop('notify_warehouse')) {
        if (notify_warehouse($orderId)) {
            $predis->lpush('notified_' . $orderId, $orderId);
        } else {
            $predis->lpush('notify_warehouse', $orderId);
        }
    } else {
        sleep(1);
    }
}
