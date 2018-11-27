<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/services.php';

// $orderId = create_order();

// $reserved = reserve_goods($orderId);

// if ($reserved) {
//     notify_warehouse($orderId);
//     $invoiceId = generate_invoice($orderId);

//     if ($invoiceId) {
//         send_mail($invoiceId);
//     } else {
//         die('FAIL - invoice is missing!' . PHP_EOL);
//     }
// } else {
//     die('FAIL - goods unavailable!' . PHP_EOL);
// }



use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Server;
use Amp\Http\Status;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Socket;
use Monolog\Logger;
// Run this script, then visit http://localhost:1337/ in your browser.
Amp\Loop::run(function () {
    $servers = [Socket\listen("0.0.0.0:8080")];

    $logHandler = new StreamHandler(new ResourceOutputStream(\STDERR));
    $logHandler->setFormatter(new ConsoleFormatter);
    $logger = new Logger('server');
    $logger->pushHandler($logHandler);
    $server = new Server($servers, new CallableRequestHandler(function (Request $request) {



        $orderId = yield Amp\call('create_order');

        $reserved = yield Amp\call('reserve_goods', $orderId);

        if ($reserved) {
            yield Amp\call('notify_warehouse', $orderId);

            $invoiceId = yield Amp\call('generate_invoice', $orderId);

            if ($invoiceId) {
                yield Amp\call('send_mail', $invoiceId);
            } else {
                die('FAIL - invoice is missing!' . PHP_EOL);
            }
        } else {
            die('FAIL - goods unavailable!' . PHP_EOL);
        }




        return new Response(Status::OK, [
            "content-type" => "text/plain; charset=utf-8"
        ], "Hello, World!");
    }), $logger);
    yield $server->start();
    // Stop the server when SIGINT is received (this is technically optional, but it is best to call Server::stop()).
    Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
        Amp\Loop::cancel($watcherId);
        yield $server->stop();
    });
});
