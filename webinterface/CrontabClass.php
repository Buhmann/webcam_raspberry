<?php

include_once 'CronjobClass.php';

class Crontab {

    var $currentCronjobs = array(); // Alle Cronjobs die in der Crontabelle enthalten sind
    var $hotiCronjobs = array();    // Alle Cronjobs die von Hoticam angelegt wurden
    var $remainingCronjobs = array();   // Alle restlichen Cronjobs von anderen Anwendungen
    var $cronfile = '../files/crontab.txt';
    var $tableRows = array();
    var $label = '_hoticam';

    function Crontab() {
        // Alte Cronjobs auslesen
        exec('sudo crontab -l', $this->currentCronjobs);
        $this->parseCrontab();
    }

    public function getTableRows() {
        return $this->tableRows;
    }

    public function parseCronjobs() {
        // Es gehoeren immer 2 zusammen, start_hoticam und stop_hoticam
        for ($i = 0; $i < count($this->hotiCronjobs); $i = $i + 2) {
            $line1 = $this->hotiCronjobs[$i];
            $cronjob1 = new Cronjob($line1);
            $line2 = $this->hotiCronjobs[$i + 1];
            $cronjob2 = new Cronjob($line2);

            $this->tableRows[] = array($cronjob1->getCronjob(), $cronjob2->getCronjob(), $cronjob1->getDayOfWeek());
        }
    }

    /*
     * Filtert alle Cronjobs raus die von Hoticam sind 
     */

    public function parseCrontab() {
        for ($i = 0; $i < count($this->currentCronjobs); $i++) {
            $line = $this->currentCronjobs[$i];
            if (strpos($line, $this->label) === false) {
                $this->remainingCronjobs[] = $line;
            } else {
                $this->hotiCronjobs[] = $line;
            }
        }
    }

    /*
     * Aktualisiert die Crontabelle mit neuen Cronjobs
     */

    public function setCronjobs($newCronjobs = array()) {
        $output = join("\n", $this->remainingCronjobs);
        
        if (count($newCronjobs) < 1) {
            
        } else {
            $newCronjobs = join("\n", $newCronjobs);
            $output = $output . "\n" . $newCronjobs;
        }

        // Neue Cronjob Datei eschreiben
        $result = file_put_contents($this->cronfile, $output . PHP_EOL);
        // Neue Datei cronetab uebergeben        
        system(sprintf("sudo crontab %s", $this->cronfile));
    }

    /*
     * Liefert alle existierenden Cronjobs die nicht von Hoticam sind
     */
//    private function getOldCronjobs($currentCronjobs) {
//        for ($i = 0; $i < count($currentCronjobs); $i++) {
//            $line = $currentCronjobs[$i];
//            if (strpos($line, 'hoticam') === false) {
//                $oldCronjobs[] = $line;
//            }
//        }
//        return $oldCronjobs;
//    }
}

?>