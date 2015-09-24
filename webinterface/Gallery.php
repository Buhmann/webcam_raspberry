<?php

class Gallery {

    var $filepath;
    var $pictures = array();
    var $maxPictures = 50;

    function Gallery($filepath) {
        $this->filepath = $filepath;
        $this->readFiles();
    }

    // Vergeleichsfunktion
    function cmp($a, $b) {
        if ($a->getKey() == $b->getKey()) {
            return 0;
        } else if ($a->getKey() > $b->getKey()) {
            return -1;
        } else {
            return 1;
        }
    }

    // Liest Bilder aus picam Ordner ein
    function readFiles() {
        $pictures = array();

        if ($handle = opendir($this->filepath)) {

            /* Das ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
            while (false !== ($file = readdir($handle))) {
                if (strlen($file) > 3) {
                    $pic = new Picture($file);
                    $pictures[] = $pic;
                }
//                    echo "$file<br>";
            }

            closedir($handle);
        }


        usort($pictures, array("Gallery", "cmp"));
        $pictures = array_slice($pictures, 0, $this->maxPictures);
        $this->setPictures($pictures);
    }

    public function getPictures() {
        return $this->pictures;
    }

    public function setPictures($pictures) {
        $this->pictures = $pictures;
    }

    public function printGallery() {
        $date = $this->pictures[0]->getDate();

        $html = array();
        $html[] = "<div id='accordion'>";
        $html[] = "<h3>" . $this->pictures[0]->getReadableDate() . "</h3>";
        $html[] = "<div>";
        foreach ($this->pictures as $value) {
            $html[] = $value->getImage();
            if ($date != $value->getDate()) {
                $date = $value->getDate();
                $html[] = "</div><h3>" . $value->getReadableDate() . "</h3><div>";
            }
        }
        $html[] = "</div>";
        $html[] = "</div>";

        return join('', $html);
    }

}

?>