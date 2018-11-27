<?php
ob_end_clean();
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

$predis = new Predis\Client;

$orderId = create_order();

$predis->lpush('reserve_goods', $orderId);
