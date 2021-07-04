<?php
header("Access-Control-Allow-Origin: http://localhost:80");
header("Access-Control-Allow-Methods: GET,POST,PUT,OPTIONS");
header("Access-Control-Allow-Headers:*");
//-- settings --//

//brainboxes serial ports
//on 'nix start with cu.usbserial-
//on windows starts with com : must be lower case in windows and end with a colon
$portName = 'COM1:';
$baudRate = 1200;
$bits = 7;
$spotBit = 1;
$parity = 1;
?>

<?php


function echoFlush($string)
{
    echo $string . "\n";
    flush();
    ob_flush();
}

if (!extension_loaded('dio')) {
    echoFlush("PHP Direct IO does not appear to be installed for more info see: http://www.php.net/manual/en/book.dio.php");
    exit;
}

try {
    //the serial port resource
    $bbSerialPort;

//    echoFlush("Connecting to serial port: {$portName}");

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $bbSerialPort = dio_open($portName, O_RDWR);
        //we're on windows configure com port from command line
        exec("mode {$portName} baud={$baudRate} data={$bits} stop={$spotBit} parity={$parity} xon=on");
    } else {
        $bbSerialPort = dio_open($portName, O_RDWR | O_NOCTTY | O_NONBLOCK);
        dio_fcntl($bbSerialPort, F_SETFL, O_SYNC);
        //we're on 'nix configure com from php direct io function
        dio_tcsetattr($bbSerialPort, array(
            'baud' => $baudRate,
            'bits' => $bits,
            'stop' => $spotBit,
            'parity' => 0

        ));
    }

    if (!$bbSerialPort) {
        echoFlush("Could not open Serial port {$portName} ");
        exit;
    }

    $runForSeconds = new DateInterval("PT10S"); //10 seconds

    $endTime = (new DateTime())->add($runForSeconds);

//  echoFlush("Waiting for {$runForSeconds->format('%S')} seconds to receive data on serial port");

    $responseData = "";

    while (new DateTime() < $endTime) {
        $responseData .= dio_read($bbSerialPort, 256); //this is a blocking call
        if (strpos($responseData, 'KG') !== false) {
            $output = preg_replace('/[^0-9]/', '', $responseData);
            echo ltrim($output, "0");
            break;
        }
    }

//  echoFlush("Closing Port");

    dio_close($bbSerialPort);

} catch (Exception $e) {
    echoFlush($e->getMessage());
    exit(1);
}
