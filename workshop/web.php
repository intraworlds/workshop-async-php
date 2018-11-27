<?php
ob_end_clean();
require __DIR__ . '/services.php';

$orderId = create_order();

$reserved = reserve_goods($orderId);

if ($reserved) {
    notify_warehouse($orderId);
    $invoiceId = generate_invoice($orderId);

    if ($invoiceId) {
        send_mail($invoiceId);
    } else {
        die('FAIL - invoice is missing!' . PHP_EOL);
    }
} else {
    die('FAIL - goods unavailable!' . PHP_EOL);
}

