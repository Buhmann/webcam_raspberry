<?php

class Settings {

    var $file = "./files/settings.ini";

    function Settings() {
        
    }

    function getSettings() {
        $settings = parse_ini_file($this->file);
        return $settings;
    }

    public function writePhpIni($array) {
        $res = array();
        $res[] = "[DEFAULT]";
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval)
                    $res[] = "$skey = " . $val; // (is_numeric($sval) ? $sval : '"' . $sval . '"');
            } else
                $res[] = "$key = " . $val; // (is_numeric($val) ? $val : '"' . $val . '"');
        }
//            var_dump($res);
//            exit();
        $this->safefilerewrite($this->file, implode("\r\n", $res));
    }

    private function safefilerewrite($fileName, $dataToSave) {
        if ($fp = fopen($fileName, 'w')) {
            $startTime = microtime();
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite)
                    usleep(round(rand(0, 100) * 1000));
            } while ((!$canWrite) and ((microtime() - $startTime) < 1000));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

    public function restoreIni() {
//        $foo= exec("rm settings.ini");
//        $foo= exec('echo $PWD');
        exec('sudo rm ./files/settings.ini');
        exec("sudo cp ./files/settings.bak ./files/settings.ini");
        exec("sudo chmod o+w ./files/settings.ini");
//        print_r($foo . " " . $bar);
//        return $foo;
    }

}

?>