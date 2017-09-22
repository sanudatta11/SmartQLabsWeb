<?php
session_start();
require_once("templates/header_template.php") ?>

<?php
if (isset($_SESSION['error'])):
    ?>
    <h2 style="color: #ce2737; padding-left: 40px; font-family: 'Shadows Into Light', cursive;" class="animated bounce">
        Oops! <?php
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </h2>
    <?php
endif;
?>

<!-- Section: contact -->
<?php
if (!isset($_SESSION['success'])):
    ?>
    <section id="service" class="home-section text-center">
        <div class="heading-contact">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="wow bounceInDown" data-wow-delay="0.4s">
                            <div class="section-heading">
                                <h2>Ads Manager.</h2>
                                <i class="fa fa-2x fa-angle-down"></i>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">

            <div class="row">
                <div class="col-lg-2 col-lg-offset-5">
                    <hr class="marginbot-50">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="boxed-grey">

                        <div id="sendmessage">Your message has been sent. Thank you!</div>
                        <div id="errormessage"></div>
                        <form id="contact-form" action="/add/post/" method="post" role="form" class="contactForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Aname">
                                            Ad Name:</label>
                                        <input type="text" name="Aname" class="form-control" id="Aname"
                                               placeholder="Enter advertisement name." data-rule="minlen:4"
                                               data-msg="Please enter at least 4 chars"/>
                                        <div class="validation"></div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="adImg">Provide image Link: </label>
                                            <input type="text" name="adImg" class="form-control" id="adImg"
                                                   placeholder="Enter image url" data-rule="minlen:4"
                                                   data-msg="Please enter at least 4 chars" onfocusout="checkImage()"
                                                   onfocusin="changeBackground()"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="adInfo">
                                            Info</label>

                                        <input type="text" name="adInfo" class="form-control" id="adInfo"
                                               placeholder="Enter details of the ad" data-rule="minlen:4"
                                               data-msg="Please enter at least 4 chars"/>
                                        <div class="validation"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="typeAd">
                                            Type of Ad</label>
                                        <input type="text" name="typeAd" class="form-control" id="typeAd"
                                               placeholder="Enter type of ad" data-rule="minlen:4"
                                               data-msg="Please enter at least 4 chars"/>
                                        <div class="validation"></div>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <h4>Mark place on the map</h4>
                                    <div id="map"></div>
                                    <br> <br>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="adInfo">
                                            Latitude</label>
                                        <input type="text" name="latAd" class="form-control" id="latAd"
                                               placeholder="Latitude"/>
                                        <div class="validation"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="adInfo">
                                            Longitude</label>
                                        <input type="text" name="longAd" class="form-control" id="longAd"
                                               placeholder="Longitude"/>
                                        <div class="validation"></div>
                                    </div>
                                </div>
                                <script>
                                    var map;
                                    function initMap() {
                                        map = new google.maps.Map(document.getElementById('map'), {
                                            center: {lat: 17.445693, lng: 78.3465994},
                                            zoom: 8
                                        });
                                        var marker;

                                        google.maps.event.addListener(map, 'click', function (event) {

                                            placeMarker(event.latLng);
                                            var coordinates = event.latLng;
                                            document.getElementById('latAd').value = coordinates.lat();
                                            document.getElementById('longAd').value = coordinates.lng();


                                        });

                                        function placeMarker(location) {

                                            if (marker == null) {
                                                marker = new google.maps.Marker({
                                                    position: location,
                                                    map: map
                                                });
                                            } else {
                                                marker.setPosition(location);
                                            }
                                        }
                                    }

                                </script>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-skin pull-right" id="btnSubmit"
                                            name="btnSubmit">
                                        Submit!
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="widget-contact">
                        <h5>Main Office</h5>

                        <address>
                            <strong>Phagwara</strong><br>
                            Punjab, India<br>
                            <abbr title="Phone">P:</abbr> (+91) 9056182515
                        </address>

                        <address>
                            <strong>Email</strong><br>
                            <a href="mailto:#">bisso.banerjee@qit.tech</a>
                        </address>
                        <address>
                            <strong>We're on social networks</strong><br>
                            <ul class="company-social">
                                <li class="social-facebook"><a href="#" target="_blank"><i
                                                class="fa fa-facebook"></i></a>
                                </li>
                                <li class="social-twitter"><a href="#" target="_blank"><i class="fa fa-twitter"></i></a>
                                </li>
                                <li class="social-dribble"><a href="#" target="_blank"><i
                                                class="fa fa-dribbble"></i></a>
                                </li>
                                <li class="social-google"><a href="#" target="_blank"><i class="fa fa-google-plus"></i></a>
                                </li>
                            </ul>
                        </address>

                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- /Section: contact -->
    <?php
endif;
?>

<?php
if ($_SESSION['success'] == True):
    ?>
    <h1 style="color: #83e861; padding-left: 40px; font-family: 'Shadows Into Light', cursive;" class="animated bounce">
        <b>Success!
            Thanks for joining QIt</b></h1>
    <?php
    unset($_SESSION['success']);
endif;
?>


<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="wow shake" data-wow-delay="0.4s">
                    <div class="page-scroll marginbot-30">
                        <a href="#intro" id="totop" class="btn btn-circle">
                            <i class="fa fa-angle-double-up animated"></i>
                        </a>
                    </div>
                </div>
                <p>&copy;Q-It. All rights reserved.</p>
                <div class="credits">

                    <a href="#">With our technology, disrupt all queues and make life user!</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<script type="text/javascript">
    function changeBackground() {
        document.getElementById('adImg').style.backgroundColor = '#ffffff';


    }
    function checkImage() {
        var flag = 0;
        var url = document.getElementById('adImg').value;
        var url2 = document.getElementById('adImg').value;
        ;
        // var x = url.length();
        var start = url2.split(":");
        console.log("value of start[0]: " + start[0]);
        if ((start[0] == "http") || (start[0] == "https")) {
            flag = 0;
        }
        else {
            flag = 1;
        }
        var extension = url.split(".").reverse();
        console.log("array at 0 index: " + extension[0]);

        if (!((extension[0] == 'png') || ((extension[0] == 'jpg')) || ((extension[0] == 'jpeg')))) {
            flag = 1;
        }
        if (flag == 1) {
            document.getElementById('adImg').style.backgroundColor = '#efd4cb';

            alert("Please verify the image url");


        }
        console.log(extension);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3JN4_CZKLGEP-XFtXzKhD-xqcc2xWu4k&callback=initMap" async
        defer></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.easing.min.js"></script>
<script src="/js/jquery.scrollTo.js"></script>
<script src="/js/wow.min.js"></script>
<!-- Custom Theme JavaScript -->
<script src="/js/custom.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/geocomplete/1.7.0/jquery.geocomplete.min.js"></script>
</body>

</html>