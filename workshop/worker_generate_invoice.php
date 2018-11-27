<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

$predis = new Predis\Client;

while (true) {
    if ($orderId = $predis->rpop('generate_invoice')) {
        $invoiceId = generate_invoice($orderId);

        if ($invoiceId) {
            $predis->lpush('send_mail', $invoiceId);
        } else {
            $predis->lpush('generate_invoice', $orderId);
        }
    } else {
        sleep(1);
    }
}
