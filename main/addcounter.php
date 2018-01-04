<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/loggedin.php';
if ($_SESSION['admin'] == 1):
    ?>

    <html>
    <head>
        <meta charset="utf-8">
        <title>Counter Manage</title>

        <!-- Compiled and minified CSS -->

        <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css"
              rel="stylesheet"/>
        <link rel="stylesheet" href="/css/counter/counter_manage.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed" rel="stylesheet">
        <!-- Compiled and minified JavaScript -->
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
        <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">

        <link rel="stylesheet" href="  https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <!-- sweet alert -->
        <script src="https://limonte.github.io/sweetalert2/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://limonte.github.io/sweetalert2/dist/sweetalert2.min.css">
    </head>
    <body>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" class="navbar-heading" style="font-family: 'Indie Flower', cursive;"> &nbsp;Smart Q Labs</a>
                <ul class="right hide-on-med-and-down">
                    <li id="increaseCounter"><a href="#"><i class="material-icons left">add</i>Add counter</a></li>
                    <li><a href="/main/dash/"><i class="material-icons left">arrow_back</i>Back to Dash</a></li>
                    <li><a href="/logout/"><i class="material-icons left">subdirectory_arrow_right</i>Log Out</a></li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="title_box">
        <h4 id="title_".$dbname."">Counter Management</h4>
    </div>
    <div class="container-fluid">
        <div class="row" id="animate_row" style="padding-bottom:-10px;">
            <div class="col s4">
                <div class="col s12">
                    <div class="card-panel ">
                        <div class="row">
                            <div class="col s12 middle">
                                Total Counters
                            </div>
                            <hr>
                            <div class="col s12 middle" id="total_counters_text">
                                00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="row">
                    <div class="col s12" id="addCounter">
                        <div class="card-panel moveUp">
                            <div class="row">
                                <div class="col s12 middle">
                                    Counter Add
                                </div>
                                <hr>
                                <div class="col s12 middle">
                                    <i class="small material-icons">add</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="col s12">
                    <div class="card-panel ">
                        <div class="row">
                            <div class="col s12 middle">
                                Counters Allocated
                            </div>
                            <hr>
                            <div class="col s12 middle" id="counters_allocated_text">
                                00
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s4">
                <div class="row" id="counterDetails" style="display: none; position: absolute;">
                    <div class="col s12">
                        <div class="card-panel moveUp" style="height: 500px;  z-index: 99 !important;">
                            <div class="row">
                                <div class="col s12 middle">
                                    Add counter details <span id="closeIt"><b>X</b></span>
                                </div>
                                <!-- <form class="" action="" method="post"> -->
                                <input type="text" id="remail" placeholder="Enter Email" style="color: white;">
                                <input type="password" id="pass" placeholder="Enter password" style="color: white;">
                                <input type="password" id="rpass" placeholder="ReEnter password" style="color: white;">
                                <div class="whiteContainer">
                                    <select style="color: white" id="group_id" class="dropdown-button">
                                            <?php
                                            $sql_query_string = "SELECT MAX(groupuid) AS max FROM " . $dbname . ".counter WHERE storeuid = :sid";
                                            $find_query = $mysql_conn->prepare($sql_query_string);
                                            $find_query->bindParam(':sid', $_SESSION['storeuid'], PDO::PARAM_INT);
                                            $find_query->execute();
                                            $find_query->setFetchMode(PDO::FETCH_ASSOC);

                                            $temp = $find_query->fetch();
                                            $tot_groups = $temp['max'];

                                            for ($i = 1; $i <= ($tot_groups + 1); ++$i) {
                                                echo '
                                                <option value="' . $i . '"><p style="color: white">Group ' . $i . '</p></option>
                                                ';
                                            }

                                            ?>
                                        </select>
                                </div>
                                <button type="button" class="waves-effect waves-light btn" style="margin-left: 65px;"
                                        id="saveCounterDetails">Done
                                </button>
                                <!-- </form> -->

                                <div class="col s2 middle">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="allCounters">

        </div>
        <br><br><br><br><br><br>


        <div class="container" id="selectoption">
        </div>
        <div class="box" id="topRight" style="height: 1000px;">
            <div class="col s12">
                <div class="card-panel-top ">
                    <div class="row">
                        <div class="col s12 middle">
                            <b> Increase counter</b>
                        </div>
                        <hr>
                        <div class="col s12 middle">
                            <input id="increase" type="number" class="validate" placeholder="Enter increase value"
                                   style="color: white;">
                            <button class="waves-effect waves-light btn blue" id="doneCounter">done</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/js/counter_manage.js"></script>
    </body>

    </html>
    <?php
else:
    header('Location: /main/');
    die();
endif;
?>
