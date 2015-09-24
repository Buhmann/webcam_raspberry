<?php

class Picture {

    var $name;
    var $key;
    var $delimiter = '-';
    var $date;
    var $time;
    var $readableDate;
    var $readableTime;
    var $imgPath = 'picam';

    function Picture($name) {
        $this->name = $name;
        $this->splitName();
    }

    function splitName() {
        $values = split($this->delimiter, $this->name);
        $date = $values[1];
        $time = $values[2];
        $this->setKey($date . $time);
        $this->setDate($date);
        $this->setTime($time);

        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        $this->setReadableDate(sprintf("%s.%s.%s", $day, $month, $year));

        $hour = substr($time, 0, 2);
        $minute = substr($time, 2, 2);
        $second = substr($time, 4, 2);
        $this->setReadableTime(sprintf("%s:%s:%s", $hour, $minute, $second));



//        echo   $this->getKey(). " " .  $this->getDate() . " " . $this->getTime() . "<br>";
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getDate() {
        return $this->date;
    }

    public function getTime() {
        return $this->time;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getName() {
        return $this->name;
    }

    public function getReadableDate() {
        return $this->readableDate;
    }

    public function setReadableDate($readableDate) {
        $this->readableDate = $readableDate;
    }

    public function getReadableTime() {
        return $this->readableTime;
    }

    public function setReadableTime($readableTime) {
        $this->readableTime = $readableTime;
    }

    public function getImage() {
        $url = $this->imgPath . "/" . $this->getName();
        return sprintf("<a href='%s' data-lightbox='%s' data-title='%s'><img src='%s' width='150px' /></a>", $url, $this->getDate(), $this->getReadableTime(), $url);
    }

}

?>