<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/9/17
 * Time: 12:44 AM
 */
session_start();
if (isset($_SESSION) && $_SESSION['admin'] == 1) {

} else {
    header('Location: /logout');
    die();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Print</title>
    <link rel="stylesheet" href="/css/qr/QRPrint.css">
    <link href="https://fonts.googleapis.com/css?family=Sura" rel="stylesheet">
    <script src="/js/qrcode.min.js "></script>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">


</head>
<body>
<div class="container-gradient">
    <h1><b>Scan the QR to Queue yourself.</b></h1>
</div>
<br>
<br>
<div class="container-gradient-second">
    <?php
    if (isset($_GET['qrcode'])) {
        $qr_code = preg_replace("/[^A-Za-z0-9]+/", "", $_GET['qrcode']);
    }
    ?>
    <input type="text" name="text" id="text" class="form-control" value="<?php
    echo $qr_code;
    ?>"/>
    <br>

    <div class="row">
        <div class="col s3">
            &nbsp;
        </div>
        <div class="col s12 m6">
            <div class="card" style="padding-top: 40px;">
                <div class="card-image">
                    <div id="qrcode" style="width: 50%;padding: 10px; margin: 0 auto; border: 2px solid #470a4d;"></div>
                </div>
                <div class="card-content" id="moveTopmost" style="text-align: center">
                    <br>
                    Scan the QR code to queue yourself on the virtual queue. <br> With QIt say no to Queue.</p>
                </div>
                <div class="card-action">
                    <a href="#" style="margin-left: 45%;" onclick="window.print();">Page Print</a>
                </div>
            </div>
        </div>
        <div class="col s3">
            &nbsp;
        </div>
    </div>

    <br><br>
</div>
<script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
<script src="/js/QR_Print.js"></script>

</body>
</html>
