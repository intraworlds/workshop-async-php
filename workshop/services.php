<?php

function create_order(): string {
    echo 'Creating order ... ';
    sleep(1); // simulate SQL queries
    echo ($orderId = uniqid()) . ' ... OK' . PHP_EOL;

    return $orderId;
}

function reserve_goods(string $orderId): bool {
    echo 'Reserving goods for order ' . $orderId . ' ... ';
    sleep(1); // simulate SQL queries
    echo 'OK' . PHP_EOL;

    return true;
}

function notify_warehouse(string $orderId): bool {
    echo 'Notifying warehouse about order ' . $orderId . ' ... ';
    sleep(1); // simulate calling remote service
    echo 'OK' . PHP_EOL;

    return true;
}

function generate_invoice(string $orderId): string {
    echo 'Generating invoice ... ';
    sleep(1); // simulate calling PDF library CLI tool
    echo ($invoiceId = 'INV-' . $orderId) . ' ... OK' . PHP_EOL;

    return $invoiceId;
}

function send_mail(string $invoiceId): void {
    echo 'Sending email ... ';
    sleep(1); // simulate sending an email
    echo 'OK' . PHP_EOL;
}
