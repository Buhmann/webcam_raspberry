<!doctype html>
<html>
    <head>
        <title>Hoticam -  Gallery</title>
        <link href="css/global.css" rel="stylesheet" />
        <link href="css/lightbox.css" rel="stylesheet" />
        <link href="css/gallery.css" rel="stylesheet" />
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />

        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>   
        <script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script> 
        <script type="text/javascript" src="js/lightbox.min.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                console.log("fuck");

                jQuery("#accordion").accordion({
                    active: false,
                    collapsible: true,
                    heightStyle: "content",
                });

                jQuery("#imgBack").bind('mouseover', function() {
                    jQuery('#imgBack').attr('src', 'img/back_mouseover.png');
                });
                jQuery("#imgBack").bind('mouseout', function() {
                    jQuery('#imgBack').attr('src', 'img/back.png');
                });

            });
        </script>

    </head>
    <body>
        <div class="main">
            <!--<h2>Hoticam</h2>-->
            <p class="header">             
                <img id="imgHoti" src="img/hoticam.png"/>
            </p> 
            <?php
            include_once 'Gallery.php';
            include_once 'PictureClass.php';

            $gallery = new Gallery('picam');
            echo $gallery->printGallery();
            ?>
            <div class="footer">
                <a href="index.php"><img id="imgBack" src="img/back.png"/></a>
            </div>
        </div>


    </body>
</html>

