#!/bin/bash
RESTART="/etc/init.d/hoticam.sh start"
$RESTART
logger "Cronejob: Hoticam gestarted!"
