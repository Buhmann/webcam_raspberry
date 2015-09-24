<?php

class Service {

    var $status;
    var $imgPath = 'picam';
    var $command = "sudo service hoticam.sh";

    function Service() {
        
    }

    public function startService() {
        exec(sprintf("%s %s", $this->command, "start"));
    }

    public function stopService() {
        exec(sprintf("%s %s", $this->command, "stop"));
    }

    private function parseOutput($output) {
        if (strpos($output, 'failed') === false) {
            $this->setStatus("running");
        } else {
            $this->setStatus("stopped");
        }
    }

    public function isRunning() {
        if ($this->status === "running") {
            return true;
        }

        return false;
    }

    public function getStatus() {
        $output = array();
        exec(sprintf("%s %s", $this->command, "status"), $output);
//        var_dump($output);
        $this->parseOutput(join(" ", $output));
    }

    private function setStatus($status) {
        $this->status = $status;
    }

}

?>