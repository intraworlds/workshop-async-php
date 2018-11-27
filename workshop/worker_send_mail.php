<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

$predis = new Predis\Client;

while (true) {
    if ($invoiceId = $predis->rpop('send_mail')) {
        send_mail($invoiceId);
    } else {
        sleep(1);
    }
}
