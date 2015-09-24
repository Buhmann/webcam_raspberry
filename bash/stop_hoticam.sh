#!/bin/bash
RESTART="/etc/init.d/hoticam.sh stop"
$RESTART
logger "Cronejob: Hoticam gestoppt!"
