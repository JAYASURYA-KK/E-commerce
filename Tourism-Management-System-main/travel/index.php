<?php session_start(); ?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Home | tourism_management</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/hover-min.css" rel="stylesheet" />
    <link href="css/main.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>
    <script src="js/logo-animation.js" type="text/javascript"></script>
    <style>
    /* Advanced CSS for Tourism Management - Smooth Animations Only */

    /* Body Background with Gradient */
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Raleway', sans-serif;
    }

    /* Home Container Enhanced */
    .home {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        margin: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Banner/Carousel Enhancements */
    .banner {
        border-radius: 15px;
        overflow: hidden;
        margin: 20px 0;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    }

    .carousel-inner img {
        filter: brightness(0.9) contrast(1.1);
        transition: all 0.5s ease;
    }

    .carousel-inner .item:hover img {
        filter: brightness(1.1) contrast(1.2);
        transform: scale(1.02);
    }

    /* Destination Quote Styling */
    .destinationQuote {
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradientShift 6s ease infinite;
        margin: 40px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    /* Container Grids - Service Icons */
    .containerGrids {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
        margin: 15px;
        padding: 30px 20px;
        border-radius: 20px;
        box-shadow:
            0 8px 16px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
    }

    .containerGrids::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
    }

    .containerGrids:hover::before {
        left: 100%;
    }

    .containerGrids:hover {
        transform: translateY(-8px);
        box-shadow:
            0 20px 40px rgba(0, 0, 0, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        border: 2px solid rgba(255, 255, 255, 0.8);
    }

    /* Service Icons Styling */
    .icons .iconsDim {
        width: 80px;
        height: 80px;
        transition: all 0.3s ease;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        border-radius: 50%;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
    }

    /* Individual Icon Colors */
    .containerGrids:nth-child(2) .icons .iconsDim {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }

    .containerGrids:nth-child(3) .icons .iconsDim {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .containerGrids:nth-child(4) .icons .iconsDim {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    }

    .containerGrids:nth-child(5) .icons .iconsDim {
        background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);
    }

    .containerGrids:nth-child(6) .icons .iconsDim {
        background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
    }

    .containerGrids:nth-child(7) .icons .iconsDim {
        background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);
    }

    .containerGrids:hover .icons .iconsDim {
        transform: scale(1.1);
        filter: brightness(1.1) drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3));
    }

    /* Service Headings */
    .containerGrids .heading {
        font-size: 1.4rem;
        font-weight: 600;
        margin-top: 15px;
        color: #2c3e50;
        transition: all 0.3s ease;
    }

    .containerGrids:hover .heading {
        color: #3498db;
        transform: scale(1.05);
    }

    /* Popular Destinations Container */
    .popularDestinationsContainer {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        margin: 30px 0;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Destination Images */
    .pics .picDim {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 15px;
        transition: all 0.4s ease;
        filter: brightness(0.9) contrast(1.1);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .containerGrids:hover .pics .picDim {
        transform: scale(1.05);
        filter: brightness(1.1) contrast(1.2) saturate(1.2);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }

    /* Destination Cards for Popular Places */
    .popularDestinationsContainer .containerGrids {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(240, 248, 255, 0.9));
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
        cursor: pointer;
    }

    .popularDestinationsContainer .containerGrids::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 18px;
        padding: 2px;
        background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .popularDestinationsContainer .containerGrids:hover::after {
        opacity: 1;
    }

    /* Additional Services Grid */
    .servicesGrid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin: 40px 0;
    }

    .serviceCard {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        min-width: 200px;
        position: relative;
        overflow: hidden;
    }

    .serviceCard:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .serviceCard .serviceIcon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .serviceCard:hover .serviceIcon {
        transform: scale(1.1);
    }

    /* Footer Enhancements */
    .footerMod {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 40px 20px;
        margin-top: 50px;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.3);
    }

    .footerHeading {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #3498db;
        position: relative;
    }

    .footerHeading::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        border-radius: 2px;
    }

    .footerText {
        margin-bottom: 10px;
        line-height: 1.6;
        color: #bdc3c7;
    }

    .socialLinks div {
        margin: 8px 0;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .socialLinks .fb:hover {
        background: #3b5998;
        transform: translateX(5px);
    }

    .socialLinks .gp:hover {
        background: #dd4b39;
        transform: translateX(5px);
    }

    .socialLinks .tw:hover {
        background: #1da1f2;
        transform: translateX(5px);
    }

    .socialLinks .in:hover {
        background: #0077b5;
        transform: translateX(5px);
    }

    /* Copyright Container */
    .copyrightContainer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        text-align: center;
    }

    .copyright {
        color: #95a5a6;
        font-size: 0.9rem;
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .destinationQuote {
            font-size: 2rem;
        }

        .containerGrids {
            margin: 10px 5px;
            padding: 20px 15px;
        }

        .icons .iconsDim {
            width: 60px;
            height: 60px;
        }

        .servicesGrid {
            flex-direction: column;
            align-items: center;
        }
    }

    /* Add smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Loading Animation for Images */
    .pics .picDim,
    .icons .iconsDim {
        opacity: 0;
        animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stagger the animation for multiple elements */
    .containerGrids:nth-child(1) {
        animation-delay: 0.1s;
    }

    .containerGrids:nth-child(2) {
        animation-delay: 0.2s;
    }

    .containerGrids:nth-child(3) {
        animation-delay: 0.3s;
    }

    .containerGrids:nth-child(4) {
        animation-delay: 0.4s;
    }

    .containerGrids:nth-child(5) {
        animation-delay: 0.5s;
    }

    .containerGrids:nth-child(6) {
        animation-delay: 0.6s;
    }
    </style>

</head>

<body>

    <div class="col-xs-12 home">

        <!-- HEADER SECTION STARTS -->

        <div class="col-sm-12">

            <div class="header">

                <?php
					
					if(!isset($_SESSION["username"])) {
						include("common/headerTransparentLoggedOut.php");
					}
					else {
						include("common/headerTransparentLoggedIn.php");
					}
					
					?>

            </div> <!-- header -->

        </div> <!-- col-sm-12 -->

        <!-- HEADER SECTION ENDS -->

        <!-- carousel -->

        <div class="col-xs-12 banner">

            <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">

                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>

                <div class="carousel-inner">

                    <div class="item active">
                        <img src="images/carousel/image5.jpg" alt="Image1">
                    </div>

                    <div class="item">
                        <img src="images/carousel/image6.jpg" alt="Image2">
                    </div>

                    <div class="item">
                        <img src="images/carousel/image7.jpg" alt="Image3">
                    </div>

                </div>

                <a href="#myCarousel" class="left carousel-control" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a href="#myCarousel" class="right carousel-control" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>

            </div>

        </div> <!-- banner -->

        <!---icons---->
        <div class="col-xs-12 popularDestinationsContainer">

            <div class="col-xs-12 destinationHolder">

                <div class="col-xs-12 destinationQuote">
                    What would you like to book today?
                </div>

                <div class="col-xs-12 containerGrids hvr-buzz-out">

                    <a href="hotels.php" style="color: black;">

                        <div class="col-xs-12 icons text-center">
                            <img src="images/icons/hotel.png" alt="hotels" class="iconsDim text-center" />
                        </div>

                        <div class="col-xs-12 heading">
                            Hotels
                        </div>

                    </a>

                </div>


                <div class="col-xs-12 containerGrids hvr-buzz-out">
                    <a href="flights.php" style="color: black;">

                        <div class="col-xs-12 icons text-center">
                            <img src="images/icons/flight.png" alt="flights" class="iconsDim text-center" />
                        </div>

                        <div class="col-xs-12 heading">
                            Flights
                        </div>

                    </a>

                </div>

                <div class="col-xs-12 containerGrids hvr-buzz-out">
                    <a href="trains.php" style="color: black;">

                        <div class="col-xs-12 icons text-center">
                            <img src="images/icons/train.png" alt="trains" class="iconsDim text-center" />
                        </div>

                        <div class="col-xs-12 heading">
                            Trains
                        </div>

                    </a>

                </div>

            </div>

            <!--popular destinations-->

            <div class="col-xs-12 popularDestinationsContainer">

                <div class="col-xs-12 destinationHolder">

                    <div class="col-xs-12 destinationQuote">

                        Popular Destinations

                    </div>

                    <!-- Existing destinations with click functionality -->
                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="images/popularDestinations/imageAndaman.jpg" alt="Andaman and Nicobar"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Andaman and Nicobar
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="images/popularDestinations/imageJaisalmer.jpg" alt="Rajasthan"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Rajasthan
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="images/popularDestinations/imageKashmir.jpg" alt="Jammu and Kashmir"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Jammu and Kashmir
                        </div>
                    </div>

                    <!-- New destinations with working images and click functionality -->
                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=1" alt="Goa Beach"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Goa
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=2" alt="Kerala Backwaters"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Kerala
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=3" alt="Taj Mahal"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Agra
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=4" alt="Himachal Pradesh Mountains"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Himachal Pradesh
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=5" alt="Uttarakhand Mountains"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Uttarakhand
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=6" alt="Ladakh Landscape"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Ladakh
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=7" alt="Sikkim Mountains"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Sikkim
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=8" alt="Meghalaya Waterfalls"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Meghalaya
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=9" alt="Arunachal Pradesh"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Arunachal Pradesh
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=10" alt="Tamil Nadu Temple"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Tamil Nadu
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=11" alt="Karnataka Palace"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Karnataka
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=12" alt="Maharashtra Caves"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Maharashtra
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=13" alt="Gujarat Architecture"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Gujarat
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=14" alt="Odisha Temple"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Odisha
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=15" alt="West Bengal Culture"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            West Bengal
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=16" alt="Assam Tea Gardens"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Assam
                        </div>
                    </div>

                    <div class="col-xs-12 containerGrids hvr-buzz-out" onclick="window.location.href='hotels.php'">
                        <div class="col-xs-12 pics text-center">
                            <img src="https://picsum.photos/400/300?random=17" alt="Punjab Fields"
                                class="picDim text-center" />
                        </div>
                        <div class="col-xs-12 heading">
                            Punjab
                        </div>
                    </div>

                </div>

            </div>

        </div> <!-- home -->

        <!-- FOOTER SECTION STARTS -->

        <div class="footerMod col-sm-12">

            <div class="col-sm-4">

                <div class="footerHeading">
                    Contact Us
                </div>

                <div class="footerText">
                    chennimalai <br> tamilnadu, India
                </div>

                <div class="footerText">
                    E-mail: jsweby@gmail.com
                </div>

            </div>

            <div class="col-sm-4">
            </div>

            <div class="col-sm-4">

                <div class="footerHeading">
                    Social Links
                </div>

                <div class="socialLinks">

                    <div class="fb">
                        facebook.com/tourism_management
                    </div>

                    <div class="gp">
                        plus.google.com/tourism_management
                    </div>

                    <div class="tw">
                        twitter.com/tourism_management
                    </div>

                    <div class="in">
                        linkedin.com/tourism_management
                    </div>

                </div> <!-- social links -->

            </div>

            <div class="col-sm-12">
                <div class="copyrightContainer">
                    <div class="copyright">
                    </div>
                </div>
            </div>

        </div> <!-- footer -->

        <!-- FOOTER SECTION ENDS -->

</body>

</html>