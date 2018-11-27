#!/bin/bash
pids=()
php worker_generate_invoice.php &
pids+=($?)
php worker_notify_warehouse.php &
pids+=($?)
php worker_reserve_goods.php &
pids+=($?)
php worker_send_mail.php &
pids+=($?)

kill_workers() {
    echo "Got signal ... killing workers"
    for pid in "${pids[@]}"; do kill $pid; done
}

trap kill_workers SIGINT

wait
