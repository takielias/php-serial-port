<?php
require __DIR__ . './vendor/autoload.php';

use Sanchescom\Serial\Serial;

try {

    $serial = new Serial();

    // First we must specify the device. This works on both linux and windows (if
    // your linux serial device is /dev/ttyS0 for COM1, etc)
    $device = $serial->setDevice('COM1');

    // We can change the baud rate, parity, length, stop bits, flow control
    $device->setBaudRate(1200);
    $device->setParity("odd");
    $device->setCharacterLength(7);
    $device->setStopBits(1);
    $device->setFlowControl("none");

    // Then we need to open it
    $device->open();

    // To write into
//    $device->send('Hello!');

    // Or to read from
    $read = $device->read();

    dd($read);

    // If you want to change the configuration, the device must be closed
    $device->close();

    // We can change the baud rate
//    $device->setBaudRate(2400);
} catch (Exception $e) {
//    echo $e->getMessage();
}
