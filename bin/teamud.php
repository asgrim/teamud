<?php

declare(strict_types=1);

set_time_limit(0);

$address = '127.0.0.1';
$port = 10000;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Send instructions. */
    $msg = "\nWelcome to Tea MUD.\n" .
        "Commands are: drink tea, quit, unplug the kettle\n";
    socket_write($msgsock, $msg, strlen($msg));
    echo "New connection.\n";

    do {
        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        if (!$buf = trim($buf)) {
            continue;
        }

        switch ($buf) {
            case 'quit':
                break 2;
            case 'unplug the kettle':
                socket_close($msgsock);
                break 3;
            case 'drink tea':
                $message = 'You drink some tea.';
                break;
            default:
                $message = 'I don\'t understand. Try "quit", "drink tea", or "unplug the kettle".';
                break;

        }
        $message .= "\n";
        socket_write($msgsock, $message, strlen($message));
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);
