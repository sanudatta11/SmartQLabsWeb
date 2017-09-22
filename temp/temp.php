<html>
<head>
    <meta charset="utf-8">
    <title>Counter Manage</title>


    <!-- Compiled and minified CSS -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="counter_manage.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed" rel="stylesheet">
    <!-- Compiled and minified JavaScript -->
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
    <link rel="stylesheet" rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
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
            <a href="#" class="navbar-heading" style="font-family: 'Indie Flower', cursive;"> &nbsp;Q-It</a>
            <ul class="right hide-on-med-and-down">
                <li id="increaseCounter"><a href="#"><i class="material-icons left">add</i>Add counter</a></li>
                <li><a href="#"><i class="material-icons left">people_outline</i>Sign up</a></li>
                <li><a href="#"><i class="material-icons left">subdirectory_arrow_right</i>Log Out</a></li>
            </ul>
        </div>
    </nav>
</div>
<div class="title_box">
    <h4 id="title_qit">Counter Management</h4>
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
                        <div class="col s12 middle">
                            05
                        </div>
                    </div>
                    <script type="text/javascript">
                        console.log("LOADING SEX");
                    </script>
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
            <div class="row" id="counterDetails" style="display: none; position: absolute;">
                <div class="col s12">
                    <div class="card-panel moveUp" style="height: 500px;  z-index: 99 !important;">
                        <div class="row">
                            <div class="col s12 middle">
                                Add counter details <span id="closeIt"><b>X</b></span>
                            </div>
                            <!-- <form class="" action="" method="post"> -->
                            <input type="text" id="username" placeholder="Enter Username" style="color: white;">

                            <input type="text" id="fname" placeholder="Enter first name" style="color: white;">
                            <input type="text" id="lname" placeholder="Enter last name" style="color: white;">
                            <input type="password" id="pass" placeholder="Enter password" style="color: white;">
                            <input type="number" id="gId" placeholder="Enter group ID" style="color: white;">
                            <div class="whiteContainer">
                                <select style="color: white">
                                    <option value="saab" style="font-size: 10px;">Counter</option>
                                    <hr>
                                    <option value="volvo"><p style="color: white">Volvo</p></option>
                                    <option value="saab">Saab</option>
                                    <option value="mercedes">Mercedes</option>
                                    <option value="audi">Audi</option>
                                    <option value="audi">Audi</option>
                                    <option value="saab">Saab</option>
                                    <option value="audi">Audi</option>
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
        <div class="col s4" id="c1">
            <div class="row">
                <div class="col s12">
                    <div class="card-panel counterCard">
                        <div class="row">
                            <div class="col s12 middle">
                                Counter 1
                            </div>
                            <hr>
                            <div class="cl s12 middle">
                                <div class="row">
                                    <div class="col s10">
                                        Avin || G-121 || @avin
                                    </div>
                                    <div class="col s2" style="padding-top: 3px;">
                                        <i class="small material-icons deleteCounter" onclick="hideMe('c1')">delete</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s4" id="c2">
            <div class="row">
                <div class="col s12">
                    <div class="card-panel counterCard">
                        <div class="row">
                            <div class="col s12 middle">
                                Counter 2
                            </div>
                            <hr>
                            <div class="cl s12 middle">
                                <div class="row">
                                    <div class="col s10">
                                        Kavin || G-121 || @avin
                                    </div>
                                    <div class="col s2" style="padding-top: 3px;">
                                        <i class="small material-icons deleteCounter" onclick="hideMe('c2')">delete</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s4" id="c3">
            <div class="row">
                <div class="col s12">
                    <div class="card-panel counterCard">
                        <div class="row">
                            <div class="col s12 middle">
                                Counter 3
                            </div>
                            <hr>
                            <div class="cl s12 middle">
                                <div class="row">
                                    <div class="col s10">
                                        Rabin || G-121 || @avin
                                    </div>
                                    <div class="col s2" style="padding-top: 3px;">
                                        <i class="small material-icons deleteCounter" onclick="hideMe('c3')">delete</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<br><br><br><br><br><br>


<!-- testing material -->

</div>
</div>
</div>

<div class="container" id="selectoption">
</div>
<script src="counter_manage.js"></script>
<script type="text/javascript">
    swal(
        'Welcome!',
        'This is QIt counter manage.',
        'success'
    )
</script>
<div class="box" id="topRight" style="height: 1000px;">
    <div class="col s12">
        <div class="card-panel-top ">
            <div class="row">
                <div class="col s12 middle">
                    <b> Increase counter</b>
                </div>
                <hr>
                <div class="col s12 middle">
                    <input id="numer" type="number" class="validate" placeholder="Enter counter value"
                           style="color: white;">
                    <a class="waves-effect waves-light btn blue" id="doneCounter" onclick="hide()">done</a>
                    <script type="text/javascript">
                        function hide() {
                            document.getElementById('topRight').style.display = 'none';
                            console.log("FUCK");
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br><br><br>
</body>

</div>


</html>