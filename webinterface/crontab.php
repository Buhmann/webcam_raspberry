<!doctype html>
<html>
    <head>
        <title>Hoticam - Crontab</title>
        <link href="css/global.css" rel="stylesheet" />
        <link href="css/jquery.datetimepicker.css" rel="stylesheet" />
        <link href="css/crontab.css" rel="stylesheet" />
        <link href="css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
        <link href="css/jquery.dataTables.min.css" rel="stylesheet" />

        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>   
        <script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script> 
        <script type="text/javascript" src="js/jquery.datetimepicker.js"></script>
        <script type="text/javascript" src="js/jquery.dataTables.js"></script>

        <script type="text/javascript" src="js/crontab.js"></script>

    </head>
    <body>
        <div class="main">
            <!--<h2>Hoticam</h2>-->
            <p class="header">             
                <img id="imgHoti" src="img/hoticam.png"/>
            </p> 


            <div class="content">
                <h3>Regel erstellen</h3>
                <form id="cronjob" method="post">
                    <label for="datetimepicker_from">Von</label>
                    <input type="text" id="datetimepicker_from"/><br>

                    <label for="datetimepicker_to">Bis</label>
                    <input type="text" id="datetimepicker_to"/><br>

                    <label for="weekday">Wochentag</label>
                    <select id="weekday">
                    </select><br>

                    <input type="button" id="addRule" value="Regel hinzufügen"/>
                </form>

                <br><br>
                <h3>Definierte Regeln</h3>
                <div id="rulesWrapper">
                    <table id="rules">
                        <thead>
                            <tr>
                                <th>Von</th>
                                <th>Bis</th>
                                <th>Wochentag</th>
                                <th>L&ouml;schen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include_once 'CrontabClass.php';

                            $crontab = new Crontab();
                            $crontab->parseCronjobs();

                            foreach ($crontab->getTableRows() as $value):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $value[0]; ?>
                                    </td>
                                    <td>
                                        <?php echo $value[1]; ?>
                                    </td>
                                    <td>
                                        <?php echo $value[2]; ?>
                                    </td>
                                    <td>
                                        <?php echo '<input name="btnCronjobDelete" type="button" value="L&ouml;schen"/>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <ol id="rules">
                    </ol>
                </div>

                <input type="button" id="submit" value="Regeln übernehmen"/>
                <input type="button" id="btnExport" value="Regeln exportieren"/>
                <label for="btnImport">Import:</label>
                <input id="btnImport" value="Regeln importieren" type="file"/>

            </div>
            <br>
            <div class="footer">
                <a href="index.php"><img id="imgBack" src="img/back.png"/></a>
            </div>
        </div>


    </body>
</html>


