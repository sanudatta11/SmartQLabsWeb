<?php
session_start();
require_once("templates/header_template.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
?>
<!-- Section: contact -->

<section id="contact" class="home-section text-center">
    <div class="heading-contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="wow bounceInDown" data-wow-delay="0.4s">
                        <div class="section-heading">
                            <h3 style="color: #ce2737; padding-left: 40px; font-family: 'Shadows Into Light', cursive;"
                                class="animated bounce">
                            </h3>
                            <?php
                            if (check($_SESSION['error'])):
                                ?>
                                <h2 style="color: red">Error!</h2>
                                <h3 style="color: firebrick"><?php echo $_SESSION['error'] ?></h3>
                            <?php endif;
                            unset($_SESSION['error']);
                            ?>
                            <h2>Let's Get Together now!</h2>
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
                    <form id="contact-form" action="/signup/post/" method="post" role="form"
                          class="contactForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Cname">
                                        Company Name</label>
                                    <input type="text" name="cname" class="form-control" id="Cname"
                                           placeholder="Company Name" data-rule="minlen:4"
                                           data-msg="Please enter at least 4 chars"/>
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="Ctype">
                                        Type of company</label>
                                    <input type="text" name="ctype" class="form-control" id="Ctype"
                                           placeholder="Type of company" data-rule="minlen:4"
                                           data-msg="Please enter at least 4 chars"/>
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="imageurl">
                                        Image of company</label>
                                    <input type="text" name="cimage" class="form-control" id="cimage"
                                           placeholder="Image of company" data-rule="minlen:6"
                                           data-msg="Please enter at least 6 chars"/>
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">
                                        Email Address</label>
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" id="email"
                                               placeholder="Your Email" data-rule="email"
                                               data-msg="Please enter a valid email"/>
                                        <div class="validation"></div>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="latitude" id="latitude"
                                               placeholder="Latitude" data-rule="text"
                                               data-msg="Please enter a valid latitude"/>
                                        <div class="validation"></div>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="longitude" id="longitude"
                                               placeholder="Longitude" data-rule="text"
                                               data-msg="Please enter a valid longitude"/>
                                        <div class="validation"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="customer">Number of counters: </label>
                                        <select class="form-control" id="counter" name="counter">
                                            <option value="1">Single counter</option>
                                            <option value="5">2-5</option>
                                            <option value="10">5-10</option>
                                            <option value="50">10-50</option>
                                            <option value="100">50-100</option>
                                            <option value="200">100-200</option>
                                            <option value="400">200-400</option>
                                            <option value="500">500</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="contract">Type of contract (preferred) : </label>
                                        <select class="form-control" id="contract">
                                            <option>Only Smart Q Labs App</option>
                                            <option selected="selected">Smart Q Labs App and Smart Q Labs analytics</option>
                                            <option>Something else</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        Your First Name</label>
                                    <input type="text" name="admin_first_name" class="form-control" id="name"
                                           placeholder="Your First name" data-rule="minlen:4"
                                           data-msg="Please enter at least 4 chars"/>
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="name">
                                        Your Last Name</label>
                                    <input type="text" name="admin_last_name" class="form-control" id="name"
                                           placeholder="Your Last name" data-rule="minlen:4"
                                           data-msg="Please enter at least 4 chars"/>
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="contactnum">
                                        Contact Number</label>
                                    <input type="number" name="contactnumber" class="form-control" id="contactnum"
                                           placeholder=" Contact Number" data-rule="minlen:4"
                                           data-msg="Please enter at least 4 chars"/>
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="message">
                                        Info</label>
                                    <textarea name="info" class="form-control" id="info"
                                              style="height: 160px;"> Anything you want to share with us? </textarea>
                                    <div class="validation"></div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="password" style="align: left; padding-right: 400px;">Password</label>
                                <input type="password" name="password" class="form-control" id="password"
                                       placeholder="Password" data-rule="minlen:8"
                                       data-msg="Please enter at least 8 chars"/>
                            </div>
                            <div class="col-md-6">
                                <label for="Cpassword" style="align: left; padding-right: 200px;">
                                    Re-enter Password</label>
                                <input type="password" name="Cpassword" class="form-control" id="Cpassword"
                                       placeholder="Re-enter Password" data-rule="minlen:8"
                                       data-msg="Please enter at least 8 chars"/>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-skin pull-right" id="btnContactUs"
                                        name="btnContactUs">
                                    Submit!
                                </button>
                            </div>
                        </div>
                        <br>
                        <hr>

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
                        <a href="mailto:#">bisso.banerjee@".$dbname.".tech</a>
                    </address>
                    <address>
                        <strong>We're on social networks</strong><br>
                        <ul class="company-social">
                            <li class="social-facebook"><a href="#" target="_blank"><i
                                            class="fa fa-facebook"></i></a></li>
                            <li class="social-twitter"><a href="#" target="_blank"><i class="fa fa-twitter"></i></a>
                            </li>
                            <li class="social-dribble"><a href="#" target="_blank"><i
                                            class="fa fa-dribbble"></i></a></li>
                            <li class="social-google"><a href="#" target="_blank"><i class="fa fa-google-plus"></i></a>
                            </li>
                        </ul>
                    </address>

                </div>
            </div>
        </div>

    </div>
</section>
<?php require_once("templates/footer_template.php") ?>
