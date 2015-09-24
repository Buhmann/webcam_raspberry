<!doctype html>
<?php
include_once 'ServiceClass.php';
?>
<html>
    <head>
        <title>Hoticam</title>

        <link href="css/global.css" rel="stylesheet" />
        <link href="css/hoticam.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script> 
        <script type="text/javascript">
            function WebSocketClient() {
                var connection = new WebSocket('ws://192.168.1.17:9998');

                var btnLeft = jQuery('#area_left');
                var btnRight = jQuery('#area_right');
                var btnTop = jQuery('#area_top');
                var btnBottom = jQuery('#area_bottom');
                var btnMiddle = jQuery('#area_middle');

                var logBox = jQuery('#logBox');
                var deltaAngle = '10';
                var json = {
                    'direction': '',
                    'position': '',
                    'delta': ''
                };


                var initialize = function() {
                    $(window).unload(function() {
//                        alert("Handler for .unload() called.");
                        connection.close();
                    });


                    btnLeft.bind('click', btnLeftClick);
                    btnRight.bind('click', btnRightClick);
                    btnTop.bind('click', btnTopClick);
                    btnBottom.bind('click', btnBottomClick);
                    btnMiddle.bind('click', btnMiddleClick);


                };



                var addLogMessage = function(message) {
                    logBox.append('<p>' + message + '</p>');

                    var height = logBox[0].scrollHeight;
                    logBox.scrollTop(height);
                };

                connection.onopen = function() {
                    // Web Socket is connected, send data using send()
                    addLogMessage('Connected to Hoticam!');
                    connection.send("Message to send");
                    initialize();

                    console.log("Message is sent...");
                };
                connection.onmessage = function(evt) {
                    var received_msg = evt.data;
                    console.log("Message is received..." + evt.data);
                    addLogMessage('Message received: ' + evt.data);

                };
                connection.onclose = function() {
                    // websocket is closed.
                    console.log("Connection is closed...");
                };

                var btnLeftClick = function() {
                    console.log("send click event..");
                    json.direction = 'h';
                    json.delta = deltaAngle;
                    addLogMessage('Camera move left!');
                    connection.send(JSON.stringify(json));
                };
                var btnRightClick = function() {
                    console.log("send click event..");
                    addLogMessage('Camera move right!');
                    json.direction = 'h';
                    json.delta = -1 * deltaAngle;
                    connection.send(JSON.stringify(json));
                };
                var btnTopClick = function() {
                    console.log("send click event..");
                    addLogMessage('Camera move top!');
                    json.direction = 'v';
                    json.delta = deltaAngle;
                    connection.send(JSON.stringify(json));
                };
                var btnBottomClick = function() {
                    console.log("send click event..");
                    addLogMessage('Camera move bottom!');
                    json.direction = 'v';
                    json.delta = -1 * deltaAngle;
                    connection.send(JSON.stringify(json));
                };

                var btnMiddleClick = function() {
                    console.log("send click event..");
                    addLogMessage('Camera starting position!');
                    json.direction = 's';
                    json.delta = 0;
                    connection.send(JSON.stringify(json));
                };

            }


            jQuery(document).ready(function($) {
                var ws;
                console.log("done");

                jwplayer('video-jwplayer').setup({
                    flashplayer: "/jwplayer/jwplayer.flash.swf"
                            , file: "rtmp://" + window.location.hostname + "/flvplayback/flv:myStream.flv"
                            , autoStart: true
                            , rtmp: {
                        bufferlength: 0.1
                    }
                    , deliveryType: "streaming"
                            , width: 960
                            , height: 540
                            , player: {
                        modes: {
                            linear: {
                                controls: {
                                    stream: {
                                        manage: false
                                                , enabled: false
                                    }
                                }
                            }
                        }
                    }
                    , shows: {
                        streamTimer: {
                            enabled: true
                                    , tickRate: 100
                        }
                    }
                });

                var changeImage = function() {
                    var direction = jQuery(this).attr('name');
                    var imgPath = '';
                    switch (direction) {
                        case "left":
                            imgPath = 'img/dpad_left.png';
                            break;
                        case "right":
                            imgPath = 'img/dpad_right.png';
                            break;
                        case "top":
                            imgPath = 'img/dpad_top.png';
                            break;
                        case "bottom":
                            imgPath = 'img/dpad_bottom.png';
                            break;
                        case "middle":
                            imgPath = 'img/dpad_middle.png';
                            break;
                    }
                    jQuery('#dpad_image').attr('src', imgPath);
                }

                jQuery("[name='controlpad'] area").bind('mouseout', function() {
                    jQuery('#dpad_image').attr('src', 'img/dpad.png');
                });

                jQuery("[name='controlpad'] area").bind('mouseover', changeImage);

                jQuery("#imgGallery").bind('mouseover', function() {
                    jQuery('#imgGallery').attr('src', 'img/gallery_mouseover.png');
                });
                jQuery("#imgGallery").bind('mouseout', function() {
                    jQuery('#imgGallery').attr('src', 'img/gallery.png');
                });

                jQuery("#imgSettings").bind('mouseover', function() {
                    jQuery('#imgSettings').attr('src', 'img/settings_mouseover.png');
                });
                jQuery("#imgSettings").bind('mouseout', function() {
                    jQuery('#imgSettings').attr('src', 'img/settings.png');
                });


                jQuery("#imgCrontab").bind('mouseover', function() {
                    jQuery('#imgCrontab').attr('src', 'img/cronjob_mouseover.png');
                });
                jQuery("#imgCrontab").bind('mouseout', function() {
                    jQuery('#imgCrontab').attr('src', 'img/cronjob.png');
                });





//                jQuery("[name='controlpad'] area").click(function() {
//                    var s = jQuery(this).attr("name");
////                    regionMap(s.substr(s.length - 2));
//                    console.log("onclick " + s);
//
//                });
                jQuery('#imgStatus').bind('click', function() {
                    var imgStop = "img/button_stop.png";
                    var imgStart = "img/button_start.png";

                    var image = $(this).attr('src');
                    var command = "";

                    if (image.indexOf("stop") === -1) {
                        // Dienst ist aktuell beendet
                        command = "start";
                    } else {
                        command = "stop";
                    }

                    // Daten senden
                    $.ajax({
                        type: "POST",
                        url: "ajax/service.php",
                        data: {command: command},
                        success: function(data) {
                            if (command === "start") {
                                jQuery("#imgStatus").attr('src', imgStop);
                                window.location = "index.php";
                            } else {
                                jQuery("#imgStatus").attr('src', imgStart);
                            }
                            console.log(data);

                        }
                    });

                });


                if (!("WebSocket" in window)) {
                    alert('<p>Oh no, you need a browser that supports WebSockets. How about <a href="http://www.google.com/chrome">Google Chrome</a>?</p>');
                } else {
                    ws = new WebSocketClient();

                }




            });



        </script>


    </head>

    <body>
        <div class="main">
            <!--<h2>Hoticam</h2>-->
            <p class="header">             
                <img id="imgHoti" src="img/hoticam.png"/>
                <?php
                $service = new Service();
                $service->getStatus();
                if ($service->isRunning()):
                    ?>
                    <img id="imgStatus" src="img/button_stop.png"/>
                    <?php
                else:
                    ?>
                    <img id="imgStatus" src="img/button_start.png"/>
                <?php
                endif;
                ?>
            </p> 


            <div id="control">

                <div id="map">
                    <img id="dpad_image" src='img/dpad.png' usemap="#controlpad"/>
                    <map name="controlpad">
                        <area id="area_left" name="left" shape="rect" coords="0,66,75,135" >
                        <area id="area_top" name="top" shape="rect" coords="67,0,135,75" >
                        <area id="area_right" name="right" shape="rect" coords="125,70,200,130"  >
                        <area id="area_bottom" name="bottom" shape="rect" coords="70,130,130,200" >
                        <area id="area_middle" name="middle" shape="rect" coords="73,73,126,126" >
                    </map>
                </div>

                <!--                <button id="btnLeft" value="Send">links</button>
                                <button id="btnTop" value="Send">oben</button>
                                <button id="btnRight" value="Send">rechts</button>
                                <button id="btnBottom" value="Send">unten</button>-->

                <div id="logBox">
                    <p>
                        Statuslog:
                    </p>
                </div>
            </div>

            <div id="video-jwplayer_wrapper" style="position: relative; display: block; width: 960px; height: 540px;">
                <object type="application/x-shockwave-flash" data="/jwplayer/jwplayer.flash.swf" width="100%" height="100%" bgcolor="#000000" id="video-jwplayer" name="video-jwplayer" tabindex="0">
                    <param name="allowfullscreen" value="true">
                    <param name="allowscriptaccess" value="always">
                    <param name="seamlesstabbing" value="true">
                    <param name="wmode" value="opaque">
                </object>
                <div id="video-jwplayer_aspect" style="display: none;"></div>
                <div id="video-jwplayer_jwpsrv" style="position: absolute; top: 0px; z-index: 10;"></div>
            </div>

            <div class="footer">
                <a href="picam.php"><img id="imgGallery" src="img/gallery.png"/></a>
                <a href="settings.php"><img id="imgSettings" src="img/settings.png"/></a>
                <a href="crontab.php"><img id="imgCrontab" src="img/cronjob.png"/></a>
            </div>


            <script src="jwplayer/jwplayer.js"></script>
            <script type="text/javascript">jwplayer.key = "NN9kKR7dVayVaj+JalZYHouM5CymHOtvXhiGdg==";</script>
        </div>
    </body>
</html>

