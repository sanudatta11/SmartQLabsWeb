<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once("templates/header_template.php");

if(isset($_SESSION["admin"]))
{
    header('Location: /main/dash/');
    die();
}
?>
<!-- /Section: intro -->

<!-- Section: about -->
<section id="about" class="home-section text-center">
    <div class="heading-about">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="wow bounceInDown" data-wow-delay="0.4s">
                        <div class="section-heading">
                            <h2>About the team</h2>
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
            <div class="col-md-3">
                <div class="wow bounceInUp" data-wow-delay="0.2s">
                    <div class="team boxed-grey">
                        <div class="inner">
                            <h5>Soumyajit <br> Dutta </h5>
                            <p class="subtitle">Back-End Developer and DevOps</p>
                            <div class="avatar"><img src="https://image.ibb.co/jssgRk/me.jpg" alt="" class="img-responsive img-circle" style="height: 170px; width: 170px" /></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wow bounceInUp" data-wow-delay="0.5s">
                    <div class="team boxed-grey">
                        <div class="inner">
                            <h5>Biswarup <br>Banerjee</h5>
                            <p class="subtitle">Front-End Developer</p>
                            <div class="avatar"><img src="https://image.ibb.co/cNpNXQ/bisso.jpg" style="height: 170px; width: 170px" alt="" class="img-responsive img-circle" /></div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wow bounceInUp" data-wow-delay="0.8s">
                    <div class="team boxed-grey">
                        <div class="inner">
                            <h5>Shriom <br> Tripathi</h5>
                            <p class="subtitle">Android Developer</p>
                            <div class="avatar"><img src="https://preview.ibb.co/g6SgRk/shriom.jpg" style="height: 170px; width: 170px" alt="" class="img-responsive img-circle" /></div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wow bounceInUp" data-wow-delay="1s">
                    <div class="team boxed-grey">
                        <div class="inner">
                            <h5>Rohit<br> Swami</h5>
                            <p class="subtitle">PWA and Frontend Developer</p>
                            <div class="avatar"><img src="https://image.ibb.co/j8STpQ/rowhitsucks2.jpg" style="height: 170px; width: 170px" alt="" class="img-responsive img-circle" /></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Section: about -->


<!-- Section: services -->
<section id="service" class="home-section text-center bg-gray">

    <div class="heading-about">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="wow bounceInDown" data-wow-delay="0.4s">
                        <div class="section-heading">
                            <h2>Our targeted customers</h2>
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
            <div class="col-md-3">
                <div class="wow fadeInLeft" data-wow-delay="0.2s">
                    <div class="service-box">
                        <div class="service-icon">
                            <img src="/img/airport.png"
                                 height="50" width="50" alt=""/>
                        </div>
                        <div class="service-desc">
                            <h5>Airports</h5>
                            <p>Smart Q Labs is a perfect app for frequent travellers. With Smart Q Labs say no to Queue at airport check-in and check-outs.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wow fadeInUp" data-wow-delay="0.2s">
                    <div class="service-box">
                        <div class="service-icon">
                            <img src="/img/tourist1600.png" height="50"
                                 width="50" alt=""/>
                        </div>
                        <div class="service-desc">
                            <h5>Tourist Spots</h5>
                            <p>Tourists spots witness some of the longest and most tiresome queues. Use Smart Q Labs to disrupt the queue and end  the endless waiting.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wow fadeInUp" data-wow-delay="0.2s">
                    <div class="service-box">
                        <div class="service-icon">
                            <img src="/img/Graphicloads-Colorful-Long-Shadow-Bank.ico"
                                 height="50" width="50" alt=""/>
                        </div>
                        <div class="service-desc">
                            <h5>Banks</h5>
                            Tired of waiting in long queues in bank? Why not switch to Smart Q Labs and make your life a bit easier! Scan the QR code of Smart Q Labs and its done. Now just seat and relax untill your turn comes.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wow fadeInRight" data-wow-delay="0.2s">
                    <div class="service-box">
                        <div class="service-icon">
                            <img src="/img/shop-icon-23328.png" height="50"
                                 width="50" alt=""/>
                        </div>
                        <div class="service-desc">
                            <h5>Shops</h5>
                            <p>Have to wait for hours in the shop? Thats really tresome. We know. So why not come and have the Smart Q Labs experience!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h5 class="text-center"> And many more like universities, hospitals, malls and what not. <br> <br>
            Imagination is the only limit....</h5>

    </div>
</section>
<!-- /Section: services -->




<!-- Section: contact -->
<section id="contact" class="home-section text-center">
    <div class="heading-contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="wow bounceInDown" data-wow-delay="0.4s">
                        <div class="section-heading">
                            <h2>Get in touch</h2>
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
                    <form id="contact-form" action="" method="post" role="form" class="contactForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        Name</label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" data-rule="minlen:4" data-msg="Please enter at least 4 chars" />
                                    <div class="validation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">
                                        Email Address</label>
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" data-rule="email" data-msg="Please enter a valid email" />
                                        <div class="validation"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="subject">
                                        Subject</label>
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" data-rule="minlen:4" data-msg="Please enter at least 8 chars of subject" />
                                    <div class="validation"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        Message</label>
                                    <textarea class="form-control" name="message" rows="5" data-rule="required" data-msg="Please write something for us" placeholder="Message"></textarea>
                                    <div class="validation"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-skin pull-right" id="btnContactUs">
                                    Send Message</button>
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
                        <a href="mailto:#">email.name@example.com</a>
                    </address>
                    <address>
                        <strong>We're on social networks</strong><br>
                        <ul class="company-social">
                            <li class="social-facebook"><a href="#" target="_blank"><i class="fa fa-facebook"></i></a></li>
                            <li class="social-twitter"><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
                            <li class="social-dribble"><a href="#" target="_blank"><i class="fa fa-dribbble"></i></a></li>
                            <li class="social-google"><a href="#" target="_blank"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </address>

                </div>
            </div>
        </div>

    </div>
</section>
<?php
if (isset($_SESSION['error'])):
    ?>
    <script>
        console.log(htmlspecialchars($_SESSION['error']));
        swal('Sorry!', '<?php
            echo htmlspecialchars($_SESSION['error']);
            ?>', 'error');
    </script>
    <?php
    unset($_SESSION['error']);
endif;
?>
<!-- /Section: contact -->
<?php require_once("templates/footer_template.php") ?>
