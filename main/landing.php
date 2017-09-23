<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 9:39 AM
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/loggedin.php';
if ($_SESSION['admin'] == 1) {
    ?>
    <html>
    <head>
        <meta charset="utf-8">
        <title>QIt Admin Analytics</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">
        <link rel="stylesheet" href="/css/admin/admin_analytics.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed" rel="stylesheet">
        <!-- Compiled and minified JavaScript -->
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
        <link
                href="https://fonts.googleapis.com/css?family=Quicksand"
                rel="stylesheet">
        <script
                type="text/javascript"
                src="https://www.gstatic.com/charts/loader.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
        <link rel="stylesheet" href="  https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <!-- sweet alert -->
        <script src="https://limonte.github.io/sweetalert2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://limonte.github.io/sweetalert2/dist/sweetalert2.min.css">

        <?php
        $start = 0;
        $end = 1;
        $hour_rating = array();
        for ($i = 0; $i < 15; ++$i, ++$start, ++$end) {
            $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".analytics WHERE hour(time) >=:start AND hour(time)<:end AND storeuid = :sid LIMIT 1";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
            $find_obj->bindParam(':start', $start, PDO::PARAM_INT);
            $find_obj->bindParam(':end', $end, PDO::PARAM_INT);
            $find_obj->execute();
            $find_obj->setFetchMode(PDO::FETCH_ASSOC);
            $data = $find_obj->fetch();
            $hour_rating[$i] = $data['total'];
        }
        ?>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Year', 'Sales'],
                    ['6-7 am', <?php echo $hour_rating[0] ?>],
                    ['7-8 am', <?php echo $hour_rating[1] ?>],
                    ['8-9 am', <?php echo $hour_rating[2] ?>],
                    ['9-10 am', <?php echo $hour_rating[3] ?>],
                    ['10-11 am', <?php echo $hour_rating[4] ?>],
                    ['11-12 am', <?php echo $hour_rating[5] ?>],
                    ['12-1 pm', <?php echo $hour_rating[6] ?>],
                    ['1-2 pm', <?php echo $hour_rating[7] ?>],
                    ['2-3 pm', <?php echo $hour_rating[8] ?>],
                    ['3-4 pm', <?php echo $hour_rating[9] ?>],
                    ['4-5 pm', <?php echo $hour_rating[10] ?>],
                    ['5-6 pm', <?php echo $hour_rating[11] ?>],
                    ['6-7 pm', <?php echo $hour_rating[12] ?>],
                    ['7-8 pm', <?php echo $hour_rating[13] ?>],
                    ['8-9 pm', <?php echo $hour_rating[14] ?>],
                ]);

                var options = {
                    title: 'Time Slot analytics',
                    curveType: 'function',
                    legend: {position: 'bottom'}
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);
            }
        </script>

        <?php
        //Per Hour Server
        $sql_statement = "SELECT DATE(NOW()) as date";
        $find_obj = $mysql_conn->prepare($sql_statement);
        $find_obj->execute();
        $data = $find_obj->fetch();
        $date = $data['date'];
        $served_per_hour = array();
        $begin = 0;
        for ($i = 0; $i < 24; ++$i, ++$begin) {
            $sql_statement = "SELECT COUNT(*) AS total FROM qit.analytics WHERE storeuid = :sid AND time >=:start AND time < :end";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':sid', $_SESSION['storeuid']);
            $start = ($date . ' ' . '0' . $begin . ':00:00');
            $find_obj->bindParam(':start', $start);
            $end = ($date . ' ' . '0' . ($begin + 1) . ':00:00');
            $find_obj->bindParam(':end', $end);
            $find_obj->execute();
            $find_obj->setFetchMode(PDO::FETCH_ASSOC);
            $data = $find_obj->fetch();
            array_push($served_per_hour, $data['total']);
        } ?>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Time', 'Footfall per hour'],
                    ['12 am', <?php echo $served_per_hour[0];?>],
                    ['1 am', <?php echo $served_per_hour[1];?>],
                    ['2 am', <?php echo $served_per_hour[2];?>],
                    ['3 am', <?php echo $served_per_hour[3];?>],
                    ['4 am', <?php echo $served_per_hour[4];?>],
                    ['5 am', <?php echo $served_per_hour[5];?>],
                    ['6 am', <?php echo $served_per_hour[6];?>],
                    ['7 am', <?php echo $served_per_hour[7];?>],
                    ['8 am', <?php echo $served_per_hour[8];?>],
                    ['9 am', <?php echo $served_per_hour[9];?>],
                    ['10 am', <?php echo $served_per_hour[10];?>],
                    ['11 am', <?php echo $served_per_hour[11];?>],
                    ['12 pm', <?php echo $served_per_hour[12];?>],
                    ['1 pm', <?php echo $served_per_hour[13];?>],
                    ['2 pm', <?php echo $served_per_hour[14];?>],
                    ['3 pm', <?php echo $served_per_hour[15];?>],
                    ['4 pm', <?php echo $served_per_hour[16];?>],
                    ['5 pm', <?php echo $served_per_hour[17];?>],
                    ['6 pm', <?php echo $served_per_hour[18];?>],
                    ['7 pm', <?php echo $served_per_hour[19];?>],
                    ['8 pm', <?php echo $served_per_hour[20];?>],
                    ['9 pm', <?php echo $served_per_hour[21];?>],
                    ['10 pm', <?php echo $served_per_hour[22];?>],
                    ['11 pm', <?php echo $served_per_hour[23];?>],
                ]);

                var options = {
                    title: 'Hourly Analytics',
                    hAxis: {title: 'Time of the day', titleTextStyle: {color: '#333'}},
                    vAxis: {minValue: 0}
                };

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>

        <?php
        //Per Day Server
        $sql_statement = "SELECT DAYOFWEEK(NOW()) as number,DAY(now()) as today";
        $find_obj = $mysql_conn->prepare($sql_statement);
        $find_obj->execute();
        $find_obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $find_obj->fetch();
        $week_no = $data['number'];
        $today = $data['today'];
        $served_per_day = array();
        $start = $today - ($week_no - 1);
        if ($start < 1)
            $start = 1;
        $tot = 0;
        for ($i = $start; $i <= $today; ++$i, ++$tot) {
            $sql_statement = "SELECT COUNT(*) AS total FROM qit.analytics WHERE storeuid = :sid AND year(time) = year(now()) AND month(time) = month(now()) AND day(time) = :day";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':day', $i);
            $find_obj->bindParam(':sid', $_SESSION['storeuid']);
            $find_obj->execute();
            if ($find_obj->rowCount() > 0) {
                $find_obj->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_obj->fetch();
                $served_per_day[$i - $start] = $temp['total'];
            } else {
                $served_per_day[$i - $start] = 0;
            }
        }
        ?>

        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Time', 'Footfall per day'],
                    ['Sunday', <?php if ($tot >= 1)
                        echo $served_per_day[0];
                    else
                        echo "0";
                        ?>],
                    ['Monday', <?php if ($tot >= 2)
                        echo $served_per_day[1];
                    else
                        echo "0";
                        ?>],
                    ['Tuesday', <?php if ($tot >= 3)
                        echo $served_per_day[2];
                    else
                        echo "0";
                        ?>],
                    ['Wednesday', <?php if ($tot >= 4)
                        echo $served_per_day[3];
                    else
                        echo "0";
                        ?>],
                    ['Thrusday', <?php if ($tot >= 5)
                        echo $served_per_day[4];
                    else
                        echo "0";
                        ?>],
                    ['Friday', <?php if ($tot >= 6)
                        echo $served_per_day[5];
                    else
                        echo "0";
                        ?>],
                    ['Saturday', <?php if ($tot >= 7)
                        echo $served_per_day[6];
                    else
                        echo "0";
                        ?>]

                ]);

                var options = {
                    title: 'Daily Analytics',
                    hAxis: {title: 'Time of the day', titleTextStyle: {color: '#333'}},
                    vAxis: {minValue: 0}
                };

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div_2'));
                chart.draw(data, options);
            }
        </script>
        <?php
        #Served Weekly
        $week_data = array();
        $week_day = array();
        $sql_statement = "SELECT FROM_DAYS(TO_DAYS(time) -MOD(TO_DAYS(time) -1, 7)) AS day,
            COUNT(*) AS perweek
            FROM " . $dbname . ".analytics
            WHERE storeuid = :sid
            GROUP BY FROM_DAYS(TO_DAYS(time) -MOD(TO_DAYS(time) -1, 7))
            ORDER BY FROM_DAYS(TO_DAYS(time) -MOD(TO_DAYS(time) -1, 7))";
        $find_obj = $mysql_conn->prepare($sql_statement);
        $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_STR);
        $find_obj->execute();
        $i = 0;
        ?>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                <?php if ($find_obj->rowCount() > 0):
                $find_obj->setFetchMode(PDO::FETCH_ASSOC);
                $data = $find_obj->fetch();
                ?>
                var data = google.visualization.arrayToDataTable([
                        ['Time', 'Footfall per day'],
                        <?php
                        while ($data = $find_obj->fetch()):
                        ?>
                        ['<?php echo json_encode($data['day']); ?>',<?php echo (int)$data['perweek'];?>],
                        <?php
                        endwhile;
                        endif;
                        ?>
                    ])
                ;

                var options = {
                    title: 'Weekly analytics',
                    hAxis: {title: 'Time of the day', titleTextStyle: {color: '#333'}},
                    vAxis: {minValue: 0}
                };

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div_3'));
                chart.draw(data, options);
            }
        </script>
    </head>
    <body>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" class="navbar-heading" style="font-family: 'Indie Flower', cursive;"> &nbsp;Smart Q Labs</a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="/main/counters/"><i class="material-icons left">add_to_queue</i>Manage Counters</a>
                    </li>
                    <li><a href="/logout/"><i class="material-icons left">subdirectory_arrow_right</i>Log Out</a></li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="title_box">
        <h4 id="title_qit">Analytics</h4>
    </div>
    <div class="container-fluid">
        <div class="row" id="animate_row" style="padding-bottom:-10px;">
            <div class="col s4">
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel moveUp">
                            <div class="row">
                                <div class="col s12 middle">
                                    Orders served today
                                </div>
                                <hr>
                                <div class="col s12 middle">
                                    <?php
                                    #Total Served Today
                                    $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".analytics WHERE storeuid = :sid AND time > DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                                    $find_obj = $mysql_conn->prepare($sql_statement);
                                    $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                    $find_obj->execute();
                                    $data = $find_obj->fetch();
                                    echo $data['total'];
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel moveUp">
                            <div class="row">
                                <div class="col s12 middle">
                                    Orders served this week
                                </div>
                                <hr>
                                <div class="col s12 middle">
                                    <?php
                                    #Total Served This week
                                    $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".analytics WHERE storeuid = :sid AND time > DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                    $find_obj = $mysql_conn->prepare($sql_statement);
                                    $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                    $find_obj->execute();
                                    $data = $find_obj->fetch();
                                    echo $data['total'];
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel moveUp">
                            <div class="row">
                                <div class="col s12 middle">
                                    Peak Hour
                                </div>
                                <hr>
                                <div class="col s12 middle">
                                    18:00
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br><br><br><br><br><br>
            <div class="titlePush"
                 style="border: 1px solid #002642; background-color: white; height: 90px; padding-top: 10px;">

                <h4 class="middle-colored" id="title2"><b>Counter Analytics</b></h4>
                <br>
                <br>
            </div>
        </div>
        <div class="row">
            <div class="col s2">
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel2  middle enhanceUI" style="background-color: #575c82; min-height: 10px;">
                            <?php
                            #Total Waiting Customers
                            $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".live_queue WHERE storeuid = :sid";
                            $find_obj = $mysql_conn->prepare($sql_statement);
                            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                            $find_obj->execute();
                            $data = $find_obj->fetch();
                            echo $data['total'];
                            ?>
                            <hr>
                            <span class="white-text" style="font-size: 16px;  ">Waiting customers</span>
                            <br>
                            <br>
                            <?php
                            #Total Wait time Today
                            $sql_statement = "SELECT ceil(avg(wait)) AS wait FROM qit.analytics WHERE storeuid = :sid";
                            $find_obj = $mysql_conn->prepare($sql_statement);
                            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                            $find_obj->execute();
                            $data = $find_obj->fetch();
                            if (isset($data['wait']))
                                echo ceil($data['wait'] / 60);
                            else
                                echo "0"
                            ?>
                            mins
                            <hr>
                            <span class="white-text" style="font-size: 14px;  ">Average Waiting Time </span>
                            <br>
                            <br>
                            <?php
                            #Total Bounced Today
                            $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".bounced WHERE storeuid = :sid AND time > DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                            $find_obj = $mysql_conn->prepare($sql_statement);
                            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                            $find_obj->execute();
                            $data = $find_obj->fetch();
                            echo $data['total'];
                            ?>
                            <hr>
                            <span class="white-text" style="font-size: 16px;  ">Bouncing Customers Today</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s5">
                <div id="chart_div" style="width: 540px; height: 200px; "></div>
                <hr>
                <div id="chart_div_3" style="width: 540px; height: 200px; "></div>

            </div>
            <div class="col s5">
                <div id="chart_div_2" style="width: 540px; height: 200px; "></div>
                <hr>
                <div id="curve_chart" style="width: 540px; height: 200px"></div>

            </div>
        </div>
        <div class="btn-grp">
            <!--            Repeat Start-->
            <?php
            #Total Served Today
            $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".counter WHERE storeuid = :sid";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
            $find_obj->execute();
            $data = $find_obj->fetch();
            $total_counters = $data['total'];

            for ($i = 1; $i <= $total_counters; ++$i):
                $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid";
                $find_obj = $mysql_conn->prepare($sql_statement);
                $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                $find_obj->bindParam(':cid', $i, PDO::PARAM_INT);
                $find_obj->execute();
                $data = $find_obj->fetch();
                $data_live = $data['total'];

                $sql_statement = "SELECT ceil(avg(wait)) AS total FROM " . $dbname . ".analytics WHERE storeuid = :sid AND counteruid = :cid";
                $find_obj = $mysql_conn->prepare($sql_statement);
                $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                $find_obj->bindParam(':cid', $i, PDO::PARAM_INT);
                $find_obj->execute();
                $data_ana = $find_obj->fetch();
                $data_wait = $data_ana['total'];
                ?>
                <a class="btn tooltip" data-position="top" data-delay="50"
                   data-tooltip="I am a tooltip"><b>Counter-<?php echo $i; ?>:</b>
                    <?php
                    echo $data_live;
                    ?><span class="tooltiptext">
                    <?php
                    if (isset($data_wait))
                        echo ceil($data_wait / 60);
                    else
                        echo "0";
                    ?>
                        mins
                </span>
                </a>

                <?php
            endfor;
            ?>

        </div>

    </div>
        <script src="/js/admin_analytics.js"></script>

    </body>
    </html>
    <?php
} elseif ($_SESSION['admin'] == 2 && isset($_SESSION['counteruid']) && !empty($_SESSION['counteruid'])) {
    ?>
    <html>
    <head>
        <meta charset="utf-8">
        <title>QIt Seller Dashboard</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">
        <link rel="stylesheet" href="/css/seller/seller_dash.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed" rel="stylesheet">
        <!-- Compiled and minified JavaScript -->
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
        <link
                href="https://fonts.googleapis.com/css?family=Quicksand"
                rel="stylesheet">
        <script
                type="text/javascript"
                src="https://www.gstatic.com/charts/loader.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
        <link rel="stylesheet" href="  https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://limonte.github.io/sweetalert2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://limonte.github.io/sweetalert2/dist/sweetalert2.min.css">

        <?php
        $start = 0;
        $end = 1;
        $hour_rating = array();
        for ($i = 0; $i < 15; ++$i, ++$start, ++$end) {
            $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".analytics WHERE hour(time) >=:start AND hour(time)<:end AND storeuid = :sid LIMIT 1";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
            $find_obj->bindParam(':start', $start, PDO::PARAM_INT);
            $find_obj->bindParam(':end', $end, PDO::PARAM_INT);
            $find_obj->execute();
            $find_obj->setFetchMode(PDO::FETCH_ASSOC);
            $data = $find_obj->fetch();
            $hour_rating[$i] = $data['total'];
        }
        ?>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Year', 'Sales'],
                    ['6-7 am', <?php echo $hour_rating[0] ?>],
                    ['7-8 am', <?php echo $hour_rating[1] ?>],
                    ['8-9 am', <?php echo $hour_rating[2] ?>],
                    ['9-10 am', <?php echo $hour_rating[3] ?>],
                    ['10-11 am', <?php echo $hour_rating[4] ?>],
                    ['11-12 am', <?php echo $hour_rating[5] ?>],
                    ['12-1 pm', <?php echo $hour_rating[6] ?>],
                    ['1-2 pm', <?php echo $hour_rating[7] ?>],
                    ['2-3 pm', <?php echo $hour_rating[8] ?>],
                    ['3-4 pm', <?php echo $hour_rating[9] ?>],
                    ['4-5 pm', <?php echo $hour_rating[10] ?>],
                    ['5-6 pm', <?php echo $hour_rating[11] ?>],
                    ['6-7 pm', <?php echo $hour_rating[12] ?>],
                    ['7-8 pm', <?php echo $hour_rating[13] ?>],
                    ['8-9 pm', <?php echo $hour_rating[14] ?>],
                ]);

                var options = {
                    title: 'Time Slot analytics',
                    curveType: 'function',
                    legend: {position: 'bottom'}
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);
            }
        </script>

        <?php
        //Per Hour Server
        $sql_statement = "SELECT DATE(NOW()) as date";
        $find_obj = $mysql_conn->prepare($sql_statement);
        $find_obj->execute();
        $data = $find_obj->fetch();
        $date = $data['date'];
        $served_per_hour = array();
        $begin = 7;
        for ($i = 0; $i < 15; ++$i, ++$begin) {
            $sql_statement = "SELECT COUNT(*) AS total FROM qit.analytics WHERE storeuid = :sid AND time >=:start AND time < :end";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':sid', $_SESSION['storeuid']);
            $start = ($date . ' ' . '0' . $begin . ':00:00');
            $find_obj->bindParam(':start', $start);
            $end = ($date . ' ' . '0' . ($begin + 1) . ':00:00');
            $find_obj->bindParam(':end', $end);
            $find_obj->execute();
            $find_obj->setFetchMode(PDO::FETCH_ASSOC);
            $data = $find_obj->fetch();
            $served_per_hour[$i] = $data['total'];
        } ?>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Time', 'Footfall per hour'],
                    ['7 am', <?php echo $served_per_hour[0];?>],
                    ['8 am', <?php echo $served_per_hour[1];?>],
                    ['9 am', <?php echo $served_per_hour[2];?>],
                    ['10 am', <?php echo $served_per_hour[3];?>],
                    ['11 am', <?php echo $served_per_hour[4];?>],
                    ['12 am', <?php echo $served_per_hour[5];?>],
                    ['1 pm', <?php echo $served_per_hour[6];?>],
                    ['2 pm', <?php echo $served_per_hour[7];?>],
                    ['3 pm', <?php echo $served_per_hour[8];?>],
                    ['4 pm', <?php echo $served_per_hour[9];?>],
                    ['5 pm', <?php echo $served_per_hour[10];?>],
                    ['6 pm', <?php echo $served_per_hour[11];?>],
                    ['7 pm', <?php echo $served_per_hour[12];?>],
                    ['8 pm', <?php echo $served_per_hour[13];?>],
                    ['9 pm', <?php echo $served_per_hour[14];?>],
                ]);

                var options = {
                    title: 'Hourly Analytics',
                    hAxis: {title: 'Time of the day', titleTextStyle: {color: '#333'}},
                    vAxis: {minValue: 0}
                };

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
    </head>
    <body style="overflow-x: hidden">
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" class="navbar-heading" style="font-family: 'Indie Flower', cursive;"> &nbsp;Smart Q Lab</a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="/logout/"><i class="material-icons left">subdirectory_arrow_right</i>Log Out</a></li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="title_box">
        <h4 id="title_qit">Dashboard</h4>
    </div>
    <div class="container-fluid">
        <div class="row" id="animate_row" style="padding-bottom:-10px;">
            <div class="reload">
                <div class="col s4">
                    <div class="row">
                        <div class="col s12">
                            <div class="card-panel moveUp">
                                <div class="row">
                                    <div class="col s12 middle">
                                        Waiting Customer Count
                                    </div>
                                    <hr>
                                    <div class="col s12 middle">
                                        <?php
                                        $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid LIMIT 1";
                                        $obj = $mysql_conn->prepare($sql_statement);
                                        $obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                        $obj->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_INT);
                                        $obj->execute();
                                        $obj->setFetchMode(PDO::FETCH_ASSOC);
                                        $data = $obj->fetch();
                                        echo $data['total'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s4">
                    <div class="row">
                        <div class="col s12">
                            <div class="card-panel moveUp">
                                <div class="row">
                                    <div class="col s12 middle">
                                        Total customer Today
                                    </div>
                                    <hr>
                                    <div class="col s12 middle">
                                        <?php
                                        $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".analytics WHERE storeuid = :sid AND counteruid = :cid AND date(now()) = date(time)";
                                        $obj = $mysql_conn->prepare($sql_statement);
                                        $obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                        $obj->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_INT);
                                        $obj->execute();
                                        $obj->setFetchMode(PDO::FETCH_ASSOC);
                                        $data = $obj->fetch();
                                        echo $data['total'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s4">
                    <div class="row">
                        <div class="col s12">
                            <div class="card-panel moveUp">
                                <div class="row">
                                    <div class="col s12 middle">
                                        Bouncing off customers
                                    </div>
                                    <hr>
                                    <div class="col s12 middle">
                                        <?php
                                        $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".bounced WHERE storeuid = :sid AND counteruid = :cid AND date(now()) = date(time)";
                                        $obj = $mysql_conn->prepare($sql_statement);
                                        $obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                        $obj->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_INT);
                                        $obj->execute();
                                        $obj->setFetchMode(PDO::FETCH_ASSOC);
                                        $data = $obj->fetch();
                                        echo $data['total'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br><br><br><br><br><br>
            <div class="titlePush" style="border: 1px solid #002642; background-color: white">
                <div class="row">
                    <div class="col s4">
                        <div id="_serving">

                        </div>
                    </div>
                    <div class="col s3">
                        <h7 class="middle-colored" style="margin-bottom: 50px"><b>Orders Queued</b></h7>
                    </div>
                    <div class="col s2">
                        <button class="waves-effect waves-light btn"
                                style="padding-bottom: 10px; background-color: green" id="serve"
                                onclick="serveToggle()">Serving On
                        </button>
                        <button class="waves-effect waves-light btn"
                                style="padding-bottom: 10px; display:  block; background-color: red; display: none"
                                id="serve2" onclick="serveToggle()">Serving Off
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <div class="row" id="dashboardMain">
            <div class="col s2">
                <div class="row">
                    <div class="col s12">
                        <br><br><br>
                        <div class="card-panel2  middle enhanceUI">
                            <p class="showBlue" id="otp_header"></p>
                            <hr>
                            <span class="text" style="font-size: 16px; color:#291a44  "><b>Queue number</b></span>

                            <br>
                            <input type="number" name="getOTP" value="" id="getOTP" placeholder="Enter OTP"
                                   class="showBlue">
                            <button class="btn waves-effect waves-light" name="action" id="otp_but">Confirm
                                <i class="material-icons right">send</i>
                            </button>
                            <br>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col s7">
                <div class="card-panel card-queueList" style="background-color: white">
                    <table class="orderList bordered centered scrollTable" id="queuetable">
                        <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Queue Number</th>
                            <th>Name</th>
                            <th>Queue Code</th>
                            <th>Operations</th>
                        </tr>
                        </thead>

                        <tbody id="queue_repeat">
                        <!--    Repeat Start-->

                        <!--      Repeat End-->

                        </tbody>
                    </table>
                    <br>
                    <div class="row" id="counterSend" style="display: none">
                        <form class="" action="" method="" style="padding-bottom: 50px;">
                            <div class="col s9">
                                <select class="browser-default" style="padding-top: 10px; width: 400px"
                                        id="counter_shift">
                                    <option value="" disabled selected>Choose counter number</option>
                                    <?php
                                    $counter_no = array();
                                    $sql_statement = "SELECT * FROM " . $dbname . ".counter WHERE storeuid = :sid AND counteruid != :cid ORDER BY counteruid";
                                    $find_querry = $mysql_conn->prepare($sql_statement);
                                    $find_querry->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                    $find_querry->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_INT);
                                    $find_querry->setFetchMode(PDO::FETCH_ASSOC);

                                    try {
                                        $find_querry->execute();
                                    while ($temp = $find_querry->fetch()):
                                        ?>
                                        <option value="<?php echo $temp['counteruid']; ?>">
                                            Counter <?php echo $temp['counteruid']; ?> </option>
                                    <?php
                                    endwhile;
                                    }catch (PDOException $e)
                                    {
                                    ?>
                                        <script>
                                            swal('Fatal Error!', 'PDO Exception Counter:<?php echo $e->getMessage();?>', 'error');
                                        </script>
                                        <?php
                                    }
                                    ?>


                                </select>
                            </div>
                            <div class="col s3">
                                <a class="waves-effect waves-light btn" style="margin-right: 40px;" id="counterDone">Done</a>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
            <div class="col s2">
                <div class="container-fluid" style="paddinng-right: 50px;">
                    <div id="curve_chart" style="width: 290px; height: 210px"></div>
                    <hr style="width: 290px;">
                    <div id="chart_div" style="width: 290px; height: 210px; "></div>
                </div>
            </div>
        </div>
        <div class="btn-grp">
            <?php
            #Total Served Today
            $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".counter WHERE storeuid = :sid";
            $find_obj = $mysql_conn->prepare($sql_statement);
            $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
            $find_obj->execute();
            $data = $find_obj->fetch();
            $total_counters = $data['total'];

            for ($i = 1; $i <= $total_counters; ++$i):
                $sql_statement = "SELECT COUNT(*) AS total FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid";
                $find_obj = $mysql_conn->prepare($sql_statement);
                $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                $find_obj->bindParam(':cid', $i, PDO::PARAM_INT);
                $find_obj->execute();
                $data = $find_obj->fetch();
                $data_live = $data['total'];

                $sql_statement = "SELECT ceil(avg(wait)) AS total FROM " . $dbname . ".analytics WHERE storeuid = :sid AND counteruid = :cid";
                $find_obj = $mysql_conn->prepare($sql_statement);
                $find_obj->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                $find_obj->bindParam(':cid', $i, PDO::PARAM_INT);
                $find_obj->execute();
                $data_ana = $find_obj->fetch();
                $data_wait = $data_ana['total'];
                ?>
                <a class="btn tooltip" data-position="top" data-delay="50"
                   data-tooltip="I am a tooltip"><b>Counter-<?php echo $i; ?>:</b>
                    <?php
                    echo $data_live;
                    ?><span class="tooltiptext">
                    <?php
                    if (isset($data_wait))
                        echo $data_wait;
                    else
                        echo "0";
                    ?>
                        mins
                </span>
                </a>

                <?php
            endfor;
            ?>


        </div>
    </div>

    <script src="/js/seller_dash.js"></script>
    <script src="/js/material_shit.js"></script>

    </body>
    </html>
    <?php
} else {
    session_destroy();
    header('Location: /main/');
    die();
}
?>
