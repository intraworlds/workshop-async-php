<?php

function create_order(): string {
    echo 'Creating order ... ';  flush();
    sleep(1); // simulate SQL queries
    echo ($orderId = uniqid()) . ' ... OK' . PHP_EOL; flush();

    return $orderId;
}

function reserve_goods(string $orderId): bool {
    sleep(1); // simulate SQL queries

    if (rand(0,1)) { // simulate probability of a failure
        echo 'Reserving goods for order ' . $orderId . ' ... OK' . PHP_EOL; flush();
        return true;
    } else {
        echo 'Reserving goods for order ' . $orderId . ' ... FAILED' . PHP_EOL; flush();
        return false;
    }
}

function notify_warehouse(string $orderId): bool {
    sleep(1); // simulate calling remote service

    if (rand(0,1)) { // simulate probability of a failure
        echo 'Notifying warehouse about order ' . $orderId . ' ... OK' . PHP_EOL; flush();
        return true;
    } else {
        echo 'Notifying warehouse about order ' . $orderId . ' ... FAILED' . PHP_EOL; flush();
        return false;
    }
}

function generate_invoice(string $orderId): ?string {
    sleep(1); // simulate calling PDF library CLI tool

    if (rand(0,1)) { // simulate probability of a failure
        echo ($invoiceId = 'Generating invoice ... INV-' . $orderId) . ' ... OK' . PHP_EOL; flush();
        return $invoiceId;
    } else {
        echo 'Generating invoice ... FAILED' . PHP_EOL; flush();
        return null;
    }
}

function send_mail(string $invoiceId): void {
    echo 'Sending email ... '; flush();
    sleep(1); // simulate sending an email
    echo 'OK' . PHP_EOL; flush();
}
