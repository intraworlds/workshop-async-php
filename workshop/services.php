<?php

function create_order(): string {
    echo 'Creating order ... ';  flush();
    echo ($orderId = uniqid()) . ' ... OK' . PHP_EOL; flush();

    return $orderId;
}

function reserve_goods(string $orderId): bool {
    echo 'Reserving goods for order ' . $orderId . ' ... '; flush();
    echo 'OK' . PHP_EOL; flush();

    return true;
}

function notify_warehouse(string $orderId): bool {
    echo 'Notifying warehouse about order ' . $orderId . ' ... '; flush();
    echo 'OK' . PHP_EOL; flush();

    return true;
}

function generate_invoice(string $orderId): string {
    echo 'Generating invoice ... '; flush();
    echo ($invoiceId = 'INV-' . $orderId) . ' ... OK' . PHP_EOL; flush();

    return $invoiceId;
}

function send_mail(string $invoiceId): void {
    echo 'Sending email ... '; flush();
    echo 'OK' . PHP_EOL; flush();
}
