<?php

include_once '../ServiceClass.php';

$service = new Service();
if (isset($_POST['command'])) {
    $command = $_POST['command'];
    if ($command == "start") {
        $service->startService();
    } else {
        $service->stopService();
    }
}

echo $service->isRunning();
?>