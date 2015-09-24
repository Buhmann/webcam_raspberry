<?php

include_once '../CrontabClass.php';

$crontab = new Crontab();
if (isset($_POST['cronjobs'])) {
    $crontab->setCronjobs($_POST['cronjobs']);
} else {
    $crontab->setCronjobs();
}

?>