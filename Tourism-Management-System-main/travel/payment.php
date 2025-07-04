<?php session_start();
if(!isset($_SESSION["username"]))
{
    	header("Location:blocked.php");
   		$_SESSION['url'] = $_SERVER['REQUEST_URI']; 
}

// Check if required POST data exists or use session data as fallback
if (!isset($_POST["modeHidden"]) || empty($_POST["modeHidden"])) {
    // Try to get from session if available
    if (isset($_SESSION['booking_mode'])) {
        $_POST["modeHidden"] = $_SESSION['booking_mode'];
    } else {
        // Redirect back to booking page if no mode is specified
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<!-- HEAD TAG STARTS -->

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Payment | tourism_management</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>

<!-- HEAD TAG ENDS -->

<!-- BODY TAG STARTS -->

<body>

    <?php include("common/headerLoggedIn.php"); ?>

    <?php
		
		$mode = isset($_POST["modeHidden"]) ? $_POST["modeHidden"] : '';
		
		if($mode=="ReturnTripFlight" or $mode=="OneWayFlight") {
	
			$totalPassengers = isset($_POST["totalPassengersHidden"]) ? $_POST["totalPassengersHidden"] : 0;
		
			if($totalPassengers > 0) {
				for($i=1; $i<=$totalPassengers; $i++) {
					$name[$i] = isset($_POST["name$i"]) ? $_POST["name$i"] : '';
					$gender[$i] = isset($_POST["gender$i"]) ? $_POST["gender$i"] : '';
				}
			}
		
			$fare = isset($_POST["fareHidden"]) ? $_POST["fareHidden"] : 0;
			$type = isset($_POST["typeHidden"]) ? $_POST["typeHidden"] : '';
			$class = isset($_POST["classHidden"]) ? $_POST["classHidden"] : '';
			$origin = isset($_POST["originHidden"]) ? $_POST["originHidden"] : '';
			$destination = isset($_POST["destinationHidden"]) ? $_POST["destinationHidden"] : '';
			$depart = isset($_POST["departHidden"]) ? $_POST["departHidden"] : '';
			$return = isset($_POST["returnHidden"]) ? $_POST["returnHidden"] : '';
			$adults = isset($_POST["adultsHidden"]) ? $_POST["adultsHidden"] : 0;
			$children = isset($_POST["childrenHidden"]) ? $_POST["childrenHidden"] : 0;
			$noOfPassengers = (int)$adults + (int)$children;
		
			if($type=="Return Trip") {
				$flightNoOutbound = isset($_POST["flightNoOutboundHidden"]) ? $_POST["flightNoOutboundHidden"] : '';
				$flightNoInbound = isset($_POST["flightNoInboundHidden"]) ? $_POST["flightNoInboundHidden"] : '';
			}
			elseif($type=="One Way") {
				$flightNoOutbound = isset($_POST["flightNoOutboundHidden"]) ? $_POST["flightNoOutboundHidden"] : '';
			}
		
			if($class=="Economy Class")
				$className="Economy";
			else
				$className="Business";
		
		} // for flights
	
		elseif($mode=="hotel") {
			$fare = isset($_POST["fareHidden"]) ? $_POST["fareHidden"] : 0;
			$hotelID = isset($_POST["hotelIDHidden"]) ? $_POST["hotelIDHidden"] : 0;
	} //for hotels
	
		elseif($mode=="train") {
			$totalPassengers = isset($_POST["totalPassengersHidden"]) ? $_POST["totalPassengersHidden"] : 0;
			$fare = isset($_POST["fareHidden"]) ? $_POST["fareHidden"] : 0;
			$trainID = isset($_POST["trainIDHidden"]) ? $_POST["trainIDHidden"] : '';
			$origin = isset($_POST["originHidden"]) ? $_POST["originHidden"] : '';
			$destination = isset($_POST["destinationHidden"]) ? $_POST["destinationHidden"] : '';
			$date = isset($_POST["dateHidden"]) ? $_POST["dateHidden"] : '';
			$day = isset($_POST["dayHidden"]) ? $_POST["dayHidden"] : '';
			$class = isset($_POST["classHidden"]) ? $_POST["classHidden"] : '';
			
			if($totalPassengers > 0) {
				for($i=1; $i<=$totalPassengers; $i++) {
					$name[$i] = isset($_POST["name$i"]) ? $_POST["name$i"] : '';
					$gender[$i] = isset($_POST["gender$i"]) ? $_POST["gender$i"] : '';
				}
			}
		} //for train
	
	?>

    <div class="spacer">a</div>

    <div class="col-sm-12 paymentWrapper">

        <div class="headingOne">

            Payment

        </div>

        <div class="totalAmount">



        </div>

        <!--<div class="col-sm-3"></div>-->


        <div class="col-sm-3"></div>

        <div class="col-sm-6">

            <div class="boxCenter">

                <div class="col-sm-12 tag">

                    Card Number:

                </div>

                <div class="col-sm-12">

                    <input type="text" class="input" name="cardNumber" placeholder="Enter the card number"
                        id="cardNumber" />

                </div>

                <div class="col-sm-12 tag">

                    Name on Card:

                </div>

                <div class="col-sm-12">

                    <input type="text" class="input" name="nameOnCard" placeholder="Enter the name of the card holder"
                        id="nameOnCard" />

                </div>

                <div class="col-sm-6 tag">

                    CVV:

                </div>

                <div class="col-sm-6 tag">

                    Expiry:

                </div>

                <div class="col-sm-6">

                    <input type="password" class="inputSmall" name="cvv" placeholder="CVV" id="cvv" />

                </div>

                <div class="col-sm-6">

                    <input type="text" class="inputSmall" name="expiry" placeholder="MM/YY" id="cardExpiry" />

                </div>

                <!-- flights -->

                <?php if($mode=="ReturnTripFlight" or $mode=="OneWayFlight"): ?>

                <form action="generateTicket.php" method="POST">

                    <div class="col-sm-12 bookingButton text-center">
                        <input type="submit" class="paymentButton" value="Pay Now">
                    </div>

                    <input type="hidden" name="totalPassengersHidden"
                        value="<?php echo isset($totalPassengers) ? $totalPassengers : 0; ?>">

                    <input type="hidden" name="fareHidden" value="<?php echo isset($fare) ? $fare : 0; ?>">
                    <input type="hidden" name="typeHidden" value="<?php echo isset($type) ? $type : ''; ?>">
                    <input type="hidden" name="classHidden" value="<?php echo isset($class) ? $class : ''; ?>">
                    <input type="hidden" name="originHidden" value="<?php echo isset($origin) ? $origin : ''; ?>">
                    <input type="hidden" name="destinationHidden"
                        value="<?php echo isset($destination) ? $destination : ''; ?>">
                    <input type="hidden" name="departHidden" value="<?php echo isset($depart) ? $depart : ''; ?>">
                    <input type="hidden" name="returnHidden" value="<?php echo isset($return) ? $return : ''; ?>">
                    <input type="hidden" name="adultsHidden" value="<?php echo isset($adults) ? $adults : 0; ?>">
                    <input type="hidden" name="childrenHidden" value="<?php echo isset($children) ? $children : 0; ?>">
                    <input type="hidden" name="modeHidden" value="<?php echo $mode ?>">

                    <?php if(isset($totalPassengers) && $totalPassengers > 0): ?>
                    <?php for($i=1; $i<=$totalPassengers; $i++) {?>

                    <input type="hidden" name="nameHidden<?php echo $i; ?>"
                        value="<?php echo isset($name[$i]) ? $name[$i] : ''; ?>">
                    <input type="hidden" name="genderHidden<?php echo $i; ?>"
                        value="<?php echo isset($gender[$i]) ? $gender[$i] : ''; ?>">

                    <?php } ?>
                    <?php endif; ?>

                    <?php if(isset($type) && $type=="Return Trip") { ?>
                    <input type="hidden" name="flightNoOutboundHidden"
                        value="<?php echo isset($flightNoOutbound) ? $flightNoOutbound : ''; ?>">
                    <input type="hidden" name="flightNoInboundHidden"
                        value="<?php echo isset($flightNoInbound) ? $flightNoInbound : ''; ?>">
                    <?php } elseif(isset($type) && $type=="One Way") { ?>
                    <input type="hidden" name="flightNoOutboundHidden"
                        value="<?php echo isset($flightNoOutbound) ? $flightNoOutbound : ''; ?>">
                    <?php } ?>

                </form>

                <!-- hotels -->

                <?php elseif($mode=="hotel"): ?>

                <form action="generateReceipt.php" method="POST">

                    <div class="col-sm-12 bookingButton text-center">
                        <input type="submit" class="paymentButton" value="Pay Now">
                    </div>

                    <input type="hidden" name="hotelIDHidden" value="<?php echo isset($hotelID) ? $hotelID : 0; ?>">
                    <input type="hidden" name="fareHidden" value="<?php echo isset($fare) ? $fare : 0; ?>">
                    <input type="hidden" name="modeHidden" value="<?php echo $mode; ?>">

                </form>

                <!--trains--->
                <?php elseif($mode=="train"): ?>

                <form action="generateTrainTicket.php" method="POST">

                    <div class="col-sm-12 bookingButton text-center">

                        <input type="hidden" name="dateHidden" value="<?php echo isset($date) ? $date : ''; ?>">
                        <input type="hidden" name="dayHidden" value="<?php echo isset($day) ? $day : ''; ?>">
                        <input type="hidden" name="classHidden" value="<?php echo isset($class) ? $class : ''; ?>">
                        <input type="submit" class="paymentButton" value="Pay Now">
                    </div>

                    <input type="hidden" name="totalPassengersHidden"
                        value="<?php echo isset($totalPassengers) ? $totalPassengers : 0; ?>">
                    <input type="hidden" name="fareHidden" value="<?php echo isset($fare) ? $fare : 0; ?>">
                    <input type="hidden" name="originHidden" value="<?php echo isset($origin) ? $origin : ''; ?>">
                    <input type="hidden" name="destinationHidden"
                        value="<?php echo isset($destination) ? $destination : ''; ?>">
                    <input type="hidden" name="modeHidden" value="<?php echo $mode ?>">

                    <?php if(isset($totalPassengers) && $totalPassengers > 0): ?>
                    <?php for($i=1; $i<=$totalPassengers; $i++) {?>

                    <input type="hidden" name="nameHidden<?php echo $i; ?>"
                        value="<?php echo isset($name[$i]) ? $name[$i] : ''; ?>">
                    <input type="hidden" name="genderHidden<?php echo $i; ?>"
                        value="<?php echo isset($gender[$i]) ? $gender[$i] : ''; ?>">

                    <?php } ?>
                    <?php endif; ?>

                    <input type="hidden" name="trainIDHidden" value="<?php echo isset($trainID) ? $trainID : ''; ?>">

                </form>

                <?php else: ?>

                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        <strong>Error:</strong> Invalid booking mode or missing booking data. Please go back and try
                        again.
                    </div>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary">Go Back to Home</a>
                    </div>
                </div>

                <?php endif; ?>


            </div>

        </div>

        <div class="col-sm-3"></div>

    </div> <!-- paymentWrapper -->

    <div class="spacerLarge">.</div> <!-- just a dummy class for creating some space -->

    <?php include("common/footer.php"); ?>

</body>

<!-- BODY TAG ENDS -->

</html>