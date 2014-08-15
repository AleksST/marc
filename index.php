<?php
$start_time = microtime(true);
header('Content-Type: text/html; charset=utf-8');
ini_set('xdebug.var_display_max_depth', 12);

include_once './Marc.php';
//$path = './test_files/TEST1.ISO';

 $source = [
     'host' => '193.233.14.5',
     'port' => '9999',
     'database' => 'katb',
     'charset' => 'windows-1251',
     'syntax' => 'rusmarc',
 ];
/** @var Rusmarc $rm */
$rm = Marc::factory('rusmarc');
$rm->setRecordsLimit(50);
$records_count = count($rm->parseZServer($source, []));
?>
<html>
    <head>
        <link rel="stylesheet" href="css/marc.css">
        <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
        <script>
        $(function() {
            $(".record").each(function(){
                $(this).click(function(){
                    showRecordFull($(this));
                });
//                showRecordFull($(this));
            })
        });

        function showRecordFull(elem){
            $(elem).find(".record-full").toggleClass('view-record-full');
        }
        </script>
    </head>
    <body>
    <?php $rm->display(); ?>

	<br>
    <?php
        $time = microtime(true) - $start_time;
        printf("Records: %5d <br>", $records_count);
        printf("Max memory: %5.2f K <br>", memory_get_peak_usage()/1024);
        printf("Time: %5.3f sec <br>", $time);
        printf("Time per record: %5.2f sec <br>", $time/$records_count);
    ?>
    </body>
</html>