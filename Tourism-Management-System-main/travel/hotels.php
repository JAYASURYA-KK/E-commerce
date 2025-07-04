<?php session_start();
if(!isset($_SESSION["username"]))
{
    	header("Location:blockedBooking.php");
   		$_SESSION['url'] = $_SERVER['REQUEST_URI']; 
}
?>

<!DOCTYPE html>

<html lang="en">

<!-- HEAD TAG STARTS -->

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Hotels | tourism_management</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-select.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="js/logo-animation.js" type="text/javascript"></script>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/moment-with-locales.js"></script>
    <script src="js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript">
    $(function() {
        $('#datetimepicker5').datetimepicker({
            format: 'L',
            locale: 'en-gb',
            useCurrent: false,
            minDate: moment()
        });

        $('#datetimepicker6').datetimepicker({
            useCurrent: false,
            format: 'L',
            locale: 'en-gb'
        });

        $("#datetimepicker5").on("dp.change", function(e) {
            $('#datetimepicker6').data("DateTimePicker").minDate(e.date);
        });

        $("#datetimepicker2").on("dp.change", function(e) {
            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
        });
    });
    </script>
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

<!-- HEAD TAG ENDS -->

<!-- BODY TAG STARTS -->

<body>

    <div class="container-fluid">

        <div class="hotels col-sm-12">

            <!-- HEADER SECTION STARTS -->

            <div class="col-sm-12">

                <div class="header">

                    <?php include("common/headerTransparentLoggedIn.php"); ?>

                    <div class="col-sm-12">

                        <div class="menu text-center">

                            <ul>
                                <li class="selected">Hotels</li>
                                <a href="flights.php">
                                    <li>Flights</li>
                                </a>
                                <a href="trains.php">
                                    <li>Trains</li>
                                </a>
                            </ul>

                        </div>

                    </div>

                </div> <!-- header -->

            </div> <!-- col-sm-12 -->

            <!-- HEADER SECTION ENDS -->



            <!-- HOTELS SEARCH SECTION STARTS -->

            <div class="col-sm-12">

                <div class="search" id="searchHotel">

                    <div class="content">

                        <form action="hotelSearch.php" method="GET">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="city">City:<p> </p></label>

                                    <select id="city" data-live-search="true" class="selectpicker form-control"
                                        data-size="5" title="Select City" name="city" required>
                                        <option value="New Delhi" data-tokens="DEL New Delhi">New Delhi</option>
                                        <option value="Mumbai" data-tokens="BOM Mumbai">Mumbai</option>
                                        <option value="Kolkata" data-tokens="CCU Kolkata">Kolkata</option>
                                        <option value="Bangalore" data-tokens="BLR Bangalore">Bangalore</option>
                                        <option value="Chennai" data-tokens="MAA Chennai">Chennai</option>
                                        <option value="Pune" data-tokens="PNQ Pune">Pune</option>
                                        <option value="Kerala" data-tokens="KER Kerala">Kerala</option>
                                        <option value="Guwahati" data-tokens="GAU Guwahati">Guwahati</option>
                                        <option value="Manali" data-tokens="MAN Manali">Manali</option>
                                        <option value="Shillong" data-tokens="SHL Shillong">Shillong</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="datetime5">Check-in:<p> </p></label>
                                    <div class="input-group date" id="datetimepicker5">
                                        <input id="datetime5" type="text" class="inputDate form-control"
                                            placeholder="Select Check-in Date" name="checkIn" required />
                                        <span class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="datetime6">Check-out:<p> </p></label>
                                    <div class="input-group date" id="datetimepicker6">
                                        <input id="datetime6" type="text" class="inputDate form-control"
                                            placeholder="Select Check-out Date" name="checkOut" required />
                                        <span class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">

                                <label for="rooms">No. of rooms:<p> </p></label>
                                <div class="form-group">
                                    <select id="rooms" class="selectpicker form-control" data-size="5"
                                        title="Select no. of rooms" name="rooms" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3" id="r1">

                                <label for="room1">Room 1:<p> </p></label>
                                <div class="form-group">
                                    <select id="room1" class="selectpicker form-control" data-size="5"
                                        title="Select no. of guests" name="room1">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3" id="r2">

                                <label for="room2">Room 2:<p> </p></label>
                                <div class="form-group">
                                    <select id="room2" class="selectpicker form-control" data-size="5"
                                        title="Select no. of guests" name="room2">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3" id="r3">

                                <label for="room3">Room 3:<p> </p></label>
                                <div class="form-group">
                                    <select id="room3" class="selectpicker form-control" data-size="5"
                                        title="Select no. of guests" name="room3">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3" id="r4">

                                <label for="room3">Room 4:<p> </p></label>
                                <div class="form-group">
                                    <select id="room4" class="selectpicker form-control" data-size="5"
                                        title="Select no. of guests" name="room4">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>







                            <div class="col-sm-12 text-center">

                                <input type="submit" class="button" name="searchHotels" value="Search Hotels"
                                    id="searchHotelButton">

                            </div>

                        </form>

                    </div> <!-- content -->

                </div> <!-- search -->

            </div>

            <!-- TRAIN SEARCH SECTION ENDS -->

        </div> <!-- trains -->



        <!-- POPULAR BUS SECTION STARTS -->

        <!-- <div class="col-sm-12"> -->

        <div class="popularHotels col-sm-12">

            <div class="heading">

                Popular Cities

            </div>

            <div class="bg">


                <div class="col-sm-4">

                    <div class="listItem">

                        <div class="imageContainer text-center">

                            <img src="images/hotels/cities/NewDelhi/piccadily.jpg" alt="New Delhi Hotels">

                        </div>

                        <div class="headings">

                            New Delhi

                        </div>

                        <div class="info">

                            3-star hotels averaging ₹ 2000

                        </div>

                        <div class="info">

                            5-star hotels averaging ₹ 6500

                        </div>


                    </div> <!-- listItem 1 -->

                </div> <!-- col-sm-4 -->

                <div class="col-sm-4">

                    <div class="listItem">

                        <div class="imageContainer text-center">

                            <img src="images/hotels/cities/Mumbai/JWMarriott.jpg" alt="Mumbai Hotels">

                        </div>

                        <div class="headings">

                            Mumbai

                        </div>

                        <div class="info">

                            3-star hotels averaging ₹ 3900

                        </div>

                        <div class="info">

                            5-star hotels averaging ₹ 9700

                        </div>


                    </div> <!-- listItem 2 -->

                </div> <!-- col-sm-4 -->

                <div class="col-sm-4">

                    <div class="listItem">

                        <div class="imageContainer text-center">

                            <img src="images/hotels/cities/Kolkata/HyattRegency.jpg" alt="kolkata Hotels">

                        </div>

                        <div class="headings">

                            Kolkata

                        </div>

                        <div class="info">

                            3-star hotels averaging ₹ 3000

                        </div>

                        <div class="info">

                            5-star hotels averaging ₹ 7750

                        </div>


                    </div> <!-- listItem 3 -->

                </div> <!-- col-sm-4 -->


            </div> <!-- bg -->

        </div> <!-- popularBus -->

        <!-- </div> -->

        <!-- POPULAR BUS SECTION ENDS -->



        <!-- FOOTER SECTION STARTS -->

        <div class="footer col-sm-12">

            <div class="col-sm-4">

                <div class="footerHeading">
                    Contact Us
                </div>

                <div class="footerText">
                    Chennimalai,<br> Tamil nadu,India
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
                        Copyright &copy; 2021 Alisha Anand
                    </div>
                </div>
            </div>

        </div> <!-- footer -->

        <!-- FOOTER SECTION ENDS -->



    </div> <!-- container-fluid -->
    <script>
    (function() {
        if (!window.chatbase || window.chatbase("getState") !== "initialized") {
            window.chatbase = (...arguments) => {
                if (!window.chatbase.q) {
                    window.chatbase.q = [];
                }
                window.chatbase.q.push(arguments);
            };
            window.chatbase = new Proxy(window.chatbase, {
                get(target, prop) {
                    if (prop === "q") {
                        return target.q;
                    }
                    return (...args) => target(prop, ...args);
                },
            });
        }
        const onLoad = function() {
            const script = document.createElement("script");
            script.src = "https://www.chatbase.co/embed.min.js";
            script.id = "s6OI0Na4jrOMIUgze0iZ7";
            script.domain = "www.chatbase.co";
            document.body.appendChild(script);
        };
        if (document.readyState === "complete") {
            onLoad();
        } else {
            window.addEventListener("load", onLoad);
        }
    })();
    </script>
</body>

<!-- BODY TAG ENDS -->

</html>