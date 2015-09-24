<!doctype html>
<html>
    <head>
        <title>Hoticam -  Settings</title>
        <link href="css/global.css" rel="stylesheet" />
        <link href="css/settings.css" rel="stylesheet" />
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />

        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>   
        <script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script> 

        <script type="text/javascript">
            jQuery(document).ready(function($) {

                jQuery("#imgBack").bind('mouseover', function() {
                    jQuery('#imgBack').attr('src', 'img/back_mouseover.png');
                });
                jQuery("#imgBack").bind('mouseout', function() {
                    jQuery('#imgBack').attr('src', 'img/back.png');
                });

                jQuery('#btnDefaultSettings').bind('click', function() {
                    if (confirm('Einstellungen zurücksetzen?')) {
                        window.location = "settings.php?restart=1";
                    }
                    return false;

                });
            });
        </script>
        <?php
        include_once 'SettingsClass.php';
//        var_dump($_GET);

        $restart = isset($_GET["restart"]);
        $isSubmitted = isset($_POST["pan_start_angle"]);

        $settingsClass = new Settings();

        if ($restart) {
            $res = $settingsClass->restoreIni();           
        }

        $settings = $settingsClass->getSettings();


        $errors = array();
        if ($isSubmitted) {
            $flag = false;
            foreach ($_POST as $key => $value) {
                if (empty($value)) {
                    $flag = true;
                    $errors[] = $key . " ist leer!";
                }
                $settings[$key] = $value;
            }

            if (!$flag) {
                $settingsClass->writePhpIni($settings);
            }
        }
        ?>
    </head>
    <body>
        <div class="main">
            <!--<h2>Hoticam</h2>-->
            <p class="header">             
                <img id="imgHoti" src="img/hoticam.png"/>
            </p> 
            <div class="errors">
                <?php
                echo "<ul>";
                foreach ($errors as $value) {
                    echo "<li>" . $value . "</li>";
                }
                echo "</ul>";
                ?>
            </div>
            <form id="settingsForm" name="foo" action="settings.php" method="post">
                <fieldset>
                    <legend>Kamera</legend>

                    <label for="pan_start_angle">Startposition horzizontal</label>
                    <input name="pan_start_angle" value="<?php echo $settings["pan_start_angle"] ?>">
                    <h5>Horizontale Startposition (0 bis 180°)</h5>

                    <label for="tilt_start_angle">Startposition vertikal</label>
                    <input name="tilt_start_angle" value="<?php echo $settings["tilt_start_angle"] ?>">
                    <h5>Vertikale Startposition (0 bis 180°)</h5>
                </fieldset>
                <fieldset>
                    <legend>Bewegungsmodus</legend>
                    <label for="filepath">Pfad</label>
                    <input name="filepath" value="<?php echo $settings["filepath"] ?>">
                    <h5>Pfad in dem die Bilder gespeichert werden</h5>

                    <label for="diskSpaceToReserve">Freier Speicherplatz</label>
                    <input name="diskSpaceToReserve" value="<?php echo $settings["diskSpaceToReserve"] ?>">
                    <h5>Wenn weniger als der angegebene Speicherplatz frei ist werden alte Bilder gel&ouml;scht (Angabe in Bytes)</h5>

                    <label for="sensitivity">Sensitivität</label>
                    <input name="sensitivity" value="<?php echo $settings["sensitivity"] ?>">
                    <h5>Wieviele Pixel sich ändern müssen damit ein neues Bilder aufgenommen werden</h5>

                    <label for="saveHeight">H&ouml;he</label>
                    <input name="saveHeight" value="<?php echo $settings["saveHeight"] ?>">
                    <h5>H&ouml;he des aufgenommen Bildes</h5>

                    <label for="saveWidth">Breite</label>
                    <input name="saveWidth" value="<?php echo $settings["saveWidth"] ?>">
                    <h5>Breite des aufgenommen Bildes</h5>

                    <label for="saveQuality">Qualit&auml;t</label>
                    <input name="saveQuality" value="<?php echo $settings["saveQuality"] ?>">
                    <h5>Qualit&auml;t des aufgenommen Bildes (0 bis 100)</h5>

                </fieldset>

                <fieldset>
                    <legend>Stream</legend>                    
                    <label for="height">H&ouml;he</label>
                    <input name="height" value="<?php echo $settings["height"] ?>">
                    <h5>H&ouml;he des Streams (64 bis 1080)</h5>

                    <label for="width">Breite</label>
                    <input name="width" value="<?php echo $settings["width"] ?>">
                    <h5>Breite des Streams (64 bis 1920)</h5>

                    <label for="fps">FPS</label>
                    <input name="fps" value="<?php echo $settings["fps"] ?>">
                    <h5>Frames per second (2 bis 30)</h5>

                    <label for="bitrate">Bitrate</label>
                    <input name="bitrate" value="<?php echo $settings["bitrate"] ?>">
                    <h5>Use bits per second, so 10MBits/s would be 10000000. For H264, 1080p a high quality bitrate would be 15Mbits/s or more</h5>
                </fieldset>
                <button type="submit">Speichern</button>
                <button id="btnDefaultSettings">Defaultsettings laden</button>

            </form>

            <div class="footer">
                <a href="index.php"><img id="imgBack" src="img/back.png"/></a>
            </div>
        </div>


    </body>
</html>

