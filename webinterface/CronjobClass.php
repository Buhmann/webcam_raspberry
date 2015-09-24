<?php

class Cronjob {

    var $minute;
    var $hour;
    var $dayOfMonth;
    var $month;
    var $dayOfWeek;
    var $command;
    var $cronjob_line;
    var $delimiter = " ";
    var $readableTime;
    var $imgPath = 'picam';

    var $weekdays = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
     
    function Cronjob($line) {
        $this->cronjob_line = $line;
        $this->splitLine();
    }

    function splitLine() {
        $values = split($this->delimiter, $this->cronjob_line);
        $this->minute = $values[0];
        $this->hour = $values[1];
        $this->dayOfMonth= $values[2];
        $this->month = $values[3];
        $this->dayOfWeek= $values[4];
        
    }

    public function getDayOfWeek(){
        return $this->weekdays[$this->dayOfWeek];
    }
    
    public function getCronjob() {
        return sprintf("%d:%d",  $this->hour,$this->minute);
    }

}

?>