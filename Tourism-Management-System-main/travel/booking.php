<?php 
session_start();
if(!isset($_SESSION["username"])) {
    header("Location:blocked.php");
    $_SESSION['url'] = $_SERVER['REQUEST_URI']; 
    exit();
}

// Use your original database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectmeteor";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate booking reference
function generateBookingReference($type) {
    $prefix = strtoupper(substr($type, 0, 2));
    return $prefix . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}

// Function to save amount to database
function saveBookingAmount($conn, $data) {
    $booking_reference = generateBookingReference($data['booking_type']);
    
    $sql = "INSERT INTO booking_amounts (
        user_id, username, booking_type, booking_reference, 
        total_amount, base_amount, convenience_fee,
        origin, destination, travel_date, passengers, booking_details, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isssdddsssiss", 
            $data['user_id'], $data['username'], $data['booking_type'], 
            $booking_reference, $data['total_amount'], $data['base_amount'], 
            $data['convenience_fee'], $data['origin'], $data['destination'], 
            $data['travel_date'], $data['passengers'], $data['booking_details'], $data['status']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'booking_reference' => $booking_reference, 'id' => $conn->insert_id];
        } else {
            return ['success' => false, 'error' => $stmt->error];
        }
    }
    return ['success' => false, 'error' => 'Failed to prepare statement'];
}

$mode = isset($_POST["modeHidden"]) ? $_POST["modeHidden"] : '';

// Handle form submission for saving booking data
if (isset($_POST['confirm_booking'])) {
    $bookingData = [];
    
    if ($mode == "OneWayFlight" || $mode == "ReturnTripFlight") {
        $bookingData = [
            'user_id' => $_SESSION['user_id'] ?? 1,
            'username' => $_SESSION['username'],
            'booking_type' => 'flight',
            'total_amount' => $_POST['fareHidden'],
            'base_amount' => $_POST['fareHidden'] - 250,
            'convenience_fee' => 250.00,
            'origin' => $_POST['originHidden'],
            'destination' => $_POST['destinationHidden'],
            'travel_date' => $_POST['departHidden'],
            'passengers' => ($_POST['adultsHidden'] + $_POST['childrenHidden']),
            'booking_details' => json_encode([
                'type' => $_POST['typeHidden'],
                'class' => $_POST['classHidden'],
                'adults' => $_POST['adultsHidden'],
                'children' => $_POST['childrenHidden'],
                'outbound_flight' => $_POST['flightNoOutboundHidden'],
                'inbound_flight' => $_POST['flightNoInboundHidden'] ?? null
            ]),
            'status' => 'pending'
        ];
    } elseif ($mode == "hotel") {
        $bookingData = [
            'user_id' => $_SESSION['user_id'] ?? 1,
            'username' => $_SESSION['username'],
            'booking_type' => 'hotel',
            'total_amount' => $_POST['fareHidden'],
            'base_amount' => $_POST['fareHidden'] - 250,
            'convenience_fee' => 250.00,
            'origin' => null,
            'destination' => 'Hotel Booking',
            'travel_date' => $_SESSION["checkIn"] ?? '',
            'passengers' => $_SESSION["noOfGuests"] ?? 1,
            'booking_details' => json_encode([
                'hotel_id' => $_POST['hotelIDHidden'],
                'check_in' => $_SESSION["checkIn"] ?? '',
                'check_out' => $_SESSION["checkOut"] ?? '',
                'rooms' => $_SESSION["noOfRooms"] ?? 1,
                'guests' => $_SESSION["noOfGuests"] ?? 1
            ]),
            'status' => 'pending'
        ];
    } elseif ($mode == "train") {
        $bookingData = [
            'user_id' => $_SESSION['user_id'] ?? 1,
            'username' => $_SESSION['username'],
            'booking_type' => 'train',
            'total_amount' => $_POST['fareHidden'],
            'base_amount' => $_POST['fareHidden'] - 250,
            'convenience_fee' => 250.00,
            'origin' => $_POST['originHidden'],
            'destination' => $_POST['destinationHidden'],
            'travel_date' => $_POST['dateHidden'],
            'passengers' => $_POST['noOfPassengersHidden'],
            'booking_details' => json_encode([
                'train_id' => $_POST['trainIDHidden'],
                'class' => $_POST['classHidden'],
                'date' => $_POST['dateHidden']
            ]),
            'status' => 'pending'
        ];
    }
    
    if (!empty($bookingData)) {
        $result = saveBookingAmount($conn, $bookingData);
        if ($result['success']) {
            // Store booking data in session for payment page
            $_SESSION['booking_reference'] = $result['booking_reference'];
            $_SESSION['booking_success'] = "Booking confirmed! Reference: " . $result['booking_reference'];
            $_SESSION['booking_mode'] = $mode; // Add this line
            $_SESSION['booking_fare'] = $bookingData['total_amount']; // Add this line
            
            // Redirect based on booking type
            if ($mode == "hotel") {
                header("Location: payment.php");
            } else {
                header("Location: passengers.php");
            }
            exit();
        } else {
            $error_message = "Error saving booking: " . $result['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Confirmation | Tourism Management</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="../../google-translate-widget.js"></script>
</head>

<body>
    <?php include("common/headerLoggedIn.php"); ?>

    <div class="spacer">a</div>

    <div class="bookingWrapper">
        <div class="headingOne">
            Please review and confirm your booking
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <?php if($mode == "OneWayFlight" || $mode == "ReturnTripFlight"): ?>

        <?php
        // Flight booking data
        $type = isset($_POST["typeHidden"]) ? $_POST["typeHidden"] : '';
        $class = isset($_POST["classHidden"]) ? $_POST["classHidden"] : '';
        $origin = isset($_POST["originHidden"]) ? $_POST["originHidden"] : '';
        $destination = isset($_POST["destinationHidden"]) ? $_POST["destinationHidden"] : '';
        $depart = isset($_POST["departHidden"]) ? $_POST["departHidden"] : '';
        $return = isset($_POST["returnHidden"]) ? $_POST["returnHidden"] : '';
        $adults = (int)(isset($_POST["adultsHidden"]) ? $_POST["adultsHidden"] : 0);
        $children = (int)(isset($_POST["childrenHidden"]) ? $_POST["childrenHidden"] : 0);
        $noOfPassengers = $adults + $children;
        
        $flightNoOutbound = isset($_POST["flightNoOutboundHidden"]) ? $_POST["flightNoOutboundHidden"] : '';
        $flightNoInbound = isset($_POST["flightNoInboundHidden"]) ? $_POST["flightNoInboundHidden"] : '';
        
        $className = ($class == "Economy Class") ? "Economy" : "Business";
        $flightType = ($type == "Return Trip") ? "return" : "one_way";
        
        // Get flight details
        $outboundFlightSQL = "SELECT * FROM `flights` WHERE `flight_no` = ? LIMIT 1";
        $stmt = $conn->prepare($outboundFlightSQL);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("s", $flightNoOutbound);
        $stmt->execute();
        $outboundResult = $stmt->get_result();
        $rowOutbound = $outboundResult->fetch_assoc();
        $stmt->close();
        
        // If flight not found, get any flight
        if (!$rowOutbound) {
            $fallbackSQL = "SELECT * FROM `flights` LIMIT 1";
            $fallbackResult = $conn->query($fallbackSQL);
            if ($fallbackResult && $fallbackResult->num_rows > 0) {
                $rowOutbound = $fallbackResult->fetch_assoc();
                $flightNoOutbound = $rowOutbound['flight_no'];
            } else {
                die("No flights available in database");
            }
        }
        
        $totalFare = 0;
        $baseFare = 0;
        $rowInbound = null;
        
        if ($mode == "ReturnTripFlight" && !empty($flightNoInbound)) {
            $inboundFlightSQL = "SELECT * FROM `flights` WHERE `flight_no` = ? LIMIT 1";
            $stmt = $conn->prepare($inboundFlightSQL);
            
            if ($stmt !== false) {
                $stmt->bind_param("s", $flightNoInbound);
                $stmt->execute();
                $inboundResult = $stmt->get_result();
                $rowInbound = $inboundResult->fetch_assoc();
                $stmt->close();
            }
            
            if ($rowInbound) {
                $baseFare = $adults * ($rowOutbound["fare"] + $rowInbound["fare"]) + $children * ($rowOutbound["fare"] + $rowInbound["fare"]);
            } else {
                $baseFare = $adults * $rowOutbound["fare"] + $children * $rowOutbound["fare"];
            }
        } else {
            $baseFare = $adults * $rowOutbound["fare"] + $children * $rowOutbound["fare"];
        }
        
        $totalFare = $baseFare + 250;
        ?>

        <div class="col-sm-12 bookingFlight">
            <div class="col-sm-7">
                <div class="col-sm-12">
                    <div class="boxLeftOneWayFlight">
                        <div class="col-sm-12 mode">Departure</div>

                        <div class="col-sm-4">
                            <div class="origin"><?php echo htmlspecialchars($origin); ?></div>
                            <div class="departs">Departs <?php echo htmlspecialchars($depart); ?> at:
                                <?php echo htmlspecialchars($rowOutbound["departs"]); ?></div>
                        </div>

                        <div class="col-sm-4">
                            <div class="arrow"></div>
                        </div>

                        <div class="col-sm-4">
                            <div class="destination"><?php echo htmlspecialchars($destination); ?></div>
                            <div class="arrives">Arrives <?php echo htmlspecialchars($depart); ?> at:
                                <?php echo htmlspecialchars($rowOutbound["arrives"]); ?></div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="operator"><?php echo htmlspecialchars($rowOutbound["operator"]); ?></div>
                            <div class="operatorSubscript">Operator</div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="class"><?php echo htmlspecialchars($className); ?></div>
                            <div class="classSubscript">Class</div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="adults"><?php echo $adults; ?></div>
                            <div class="adultsSubscript">Adults</div>
                        </div>

                        <div class="col-sm-3">
                            <div class="children"><?php echo $children; ?></div>
                            <div class="childrenSubscript">Children</div>
                        </div>
                    </div>
                </div>

                <?php if ($mode == "ReturnTripFlight" && $rowInbound): ?>
                <div class="col-sm-12">
                    <div class="boxLeftOneWayFlight">
                        <div class="col-sm-12 mode">Return</div>

                        <div class="col-sm-4">
                            <div class="origin"><?php echo htmlspecialchars($rowInbound["origin"]); ?></div>
                            <div class="departs">Departs <?php echo htmlspecialchars($return); ?> at:
                                <?php echo htmlspecialchars($rowInbound["departs"]); ?></div>
                        </div>

                        <div class="col-sm-4">
                            <div class="arrow"></div>
                        </div>

                        <div class="col-sm-4">
                            <div class="destination"><?php echo htmlspecialchars($rowInbound["destination"]); ?></div>
                            <div class="arrives">Arrives <?php echo htmlspecialchars($return); ?> at:
                                <?php echo htmlspecialchars($rowInbound["arrives"]); ?></div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="operator"><?php echo htmlspecialchars($rowInbound["operator"]); ?></div>
                            <div class="operatorSubscript">Operator</div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="class"><?php echo htmlspecialchars($className); ?></div>
                            <div class="classSubscript">Class</div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="adults"><?php echo $adults; ?></div>
                            <div class="adultsSubscript">Adults</div>
                        </div>

                        <div class="col-sm-3">
                            <div class="children"><?php echo $children; ?></div>
                            <div class="childrenSubscript">Children</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-sm-5">
                <div class="col-sm-12">
                    <div class="boxRightOneWayFlight">
                        <div class="col-sm-12 fareSummary">Fare Summary</div>

                        <div class="col-sm-8">
                            <div class="heading"><?php echo $adults; ?> Adults</div>
                            <div class="heading"><?php echo $children; ?> Children</div>
                            <div class="heading">Convenience Fee</div>
                        </div>

                        <div class="col-sm-4">
                            <div class="price"><span class="sansSerif">₹
                                </span><?php echo number_format($adults * ($mode == "ReturnTripFlight" && $rowInbound ? ($rowOutbound["fare"] + $rowInbound["fare"]) : $rowOutbound["fare"]), 2); ?>
                            </div>
                            <div class="price"><span class="sansSerif">₹
                                </span><?php echo number_format($children * ($mode == "ReturnTripFlight" && $rowInbound ? ($rowOutbound["fare"] + $rowInbound["fare"]) : $rowOutbound["fare"]), 2); ?>
                            </div>
                            <div class="price"><span class="sansSerif">₹ </span>250.00</div>
                        </div>

                        <div class="col-sm-12">
                            <div class="calcBar"></div>
                        </div>

                        <div class="col-sm-8">
                            <div class="headingTotal">Total Fare</div>
                        </div>

                        <div class="col-sm-4">
                            <div class="priceTotal"><span class="sansSerif">₹
                                </span><?php echo number_format($totalFare, 2); ?></div>
                        </div>

                        <form method="POST">
                            <div class="bookingButton text-center">
                                <input type="submit" name="confirm_booking" class="confirmButton"
                                    value="Confirm Booking">
                            </div>

                            <input type="hidden" name="fareHidden" value="<?php echo $totalFare; ?>">
                            <input type="hidden" name="typeHidden" value="<?php echo $type; ?>">
                            <input type="hidden" name="classHidden" value="<?php echo $class; ?>">
                            <input type="hidden" name="originHidden" value="<?php echo htmlspecialchars($origin); ?>">
                            <input type="hidden" name="destinationHidden"
                                value="<?php echo htmlspecialchars($destination); ?>">
                            <input type="hidden" name="departHidden" value="<?php echo htmlspecialchars($depart); ?>">
                            <input type="hidden" name="returnHidden" value="<?php echo htmlspecialchars($return); ?>">
                            <input type="hidden" name="adultsHidden" value="<?php echo $adults; ?>">
                            <input type="hidden" name="childrenHidden" value="<?php echo $children; ?>">
                            <input type="hidden" name="flightNoOutboundHidden"
                                value="<?php echo htmlspecialchars($flightNoOutbound); ?>">
                            <?php if ($rowInbound): ?>
                            <input type="hidden" name="flightNoInboundHidden"
                                value="<?php echo htmlspecialchars($rowInbound["flight_no"]); ?>">
                            <?php endif; ?>
                            <input type="hidden" name="modeHidden" value="<?php echo $mode; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif($mode == "hotel"): ?>

        <?php
        // Hotel booking data
        $hotelID = isset($_POST["hotelIDHidden"]) ? (int)$_POST["hotelIDHidden"] : 0;
        
        $hotelSQL = "SELECT * FROM `hotels` WHERE `hotelID` = ? LIMIT 1";
        $stmt = $conn->prepare($hotelSQL);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $hotelID);
        $stmt->execute();
        $hotelResult = $stmt->get_result();
        $row = $hotelResult->fetch_assoc();
        $stmt->close();
        
        // If hotel not found, get any hotel
        if (!$row) {
            $fallbackSQL = "SELECT * FROM `hotels` LIMIT 1";
            $fallbackResult = $conn->query($fallbackSQL);
            if ($fallbackResult && $fallbackResult->num_rows > 0) {
                $row = $fallbackResult->fetch_assoc();
                $hotelID = $row['hotelID'];
            } else {
                die("No hotels available in database");
            }
        }
        
        $checkIn = isset($_SESSION["checkIn"]) ? $_SESSION["checkIn"] : '';
        $checkOut = isset($_SESSION["checkOut"]) ? $_SESSION["checkOut"] : '';
        $noOfRooms = isset($_SESSION["noOfRooms"]) ? (int)$_SESSION["noOfRooms"] : 1;
        $noOfGuests = isset($_SESSION["noOfGuests"]) ? (int)$_SESSION["noOfGuests"] : 1;
        
        // Calculate number of nights
        if (!empty($checkIn) && !empty($checkOut)) {
            $date1 = date_create(str_replace('/', '-', $checkIn));
            $date2 = date_create(str_replace('/', '-', $checkOut));
            if ($date1 && $date2) {
                $diff = date_diff($date1, $date2);
                $noOfDays = $diff->format("%a");
            } else {
                $noOfDays = 1;
            }
        } else {
            $noOfDays = 1;
        }
        
        $baseFare = $noOfRooms * $row["price"] * $noOfDays;
        $totalFare = $baseFare + 250;
        ?>

        <div class="col-sm-12 bookingHotel">
            <div class="col-sm-7">
                <div class="col-sm-12">
                    <div class="boxLeftHotel">
                        <div class="col-sm-12 hotelMode">Booking Summary</div>

                        <div class="col-sm-12 hotelName">
                            Name of the hotel: <span
                                class="nameText"><?php echo htmlspecialchars($row["hotelName"] . ', ' . $row["locality"] . ', ' . $row["city"]); ?></span>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="checkIn"><?php echo htmlspecialchars($checkIn); ?></div>
                            <div class="checkInSubscript">Check In Date</div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="checkOut"><?php echo htmlspecialchars($checkOut); ?></div>
                            <div class="checkOutSubscript">Check Out Date</div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="noOfRooms"><?php echo $noOfRooms; ?></div>
                            <div class="noOfRoomsSubscript">No. of rooms</div>
                        </div>

                        <div class="col-sm-3">
                            <div class="noOfGuests"><?php echo $noOfGuests; ?></div>
                            <div class="noOfGuestsSubscript">No. of guests</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="col-sm-12">
                    <div class="boxRightHotel">
                        <div class="col-sm-12 fareSummary">Payment Summary</div>

                        <div class="col-sm-8">
                            <div class="heading"><?php echo $noOfRooms; ?> Rooms x <?php echo $noOfDays; ?> Days</div>
                            <div class="heading">Convenience Fee</div>
                        </div>

                        <div class="col-sm-4">
                            <div class="price"><span class="sansSerif">₹
                                </span><?php echo number_format($baseFare, 2); ?></div>
                            <div class="price"><span class="sansSerif">₹ </span>250.00</div>
                        </div>

                        <div class="col-sm-12">
                            <div class="calcBar"></div>
                        </div>

                        <div class="col-sm-8">
                            <div class="headingTotal">Total Payment</div>
                        </div>

                        <div class="col-sm-4">
                            <div class="priceTotal"><span class="sansSerif">₹
                                </span><?php echo number_format($totalFare, 2); ?></div>
                        </div>

                        <form method="POST">
                            <div class="bookingButton text-center">
                                <input type="submit" name="confirm_booking" class="confirmButton"
                                    value="Confirm Booking">
                            </div>

                            <input type="hidden" name="fareHidden" value="<?php echo $totalFare; ?>">
                            <input type="hidden" name="hotelIDHidden" value="<?php echo $hotelID; ?>">
                            <input type="hidden" name="modeHidden" value="hotel">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif($mode == "train"): ?>

        <?php
        // Train booking data
        $trainID = isset($_POST["trainIdPass"]) ? $_POST["trainIdPass"] : '';
        $date = isset($_POST["dateHidden"]) ? $_POST["dateHidden"] : '';
        $day = isset($_POST["dayHidden"]) ? $_POST["dayHidden"] : '';
        $origin = isset($_POST["originHidden"]) ? $_POST["originHidden"] : '';
        $destination = isset($_POST["destinationHidden"]) ? $_POST["destinationHidden"] : '';
        $class = isset($_POST["classHidden"]) ? $_POST["classHidden"] : '';
        $noOfPassengers = isset($_POST["passengersHidden"]) ? (int)$_POST["passengersHidden"] : 1;
        $priceClass = 'price' . $class;
        
        $trainSQL = "SELECT * FROM `trains` WHERE `trainNo` = ? LIMIT 1";
        $stmt = $conn->prepare($trainSQL);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("s", $trainID);
        $stmt->execute();
        $trainResult = $stmt->get_result();
        $row = $trainResult->fetch_assoc();
        $stmt->close();
        
        // If train not found, get any train
        if (!$row) {
            $fallbackSQL = "SELECT * FROM `trains` LIMIT 1";
            $fallbackResult = $conn->query($fallbackSQL);
            if ($fallbackResult && $fallbackResult->num_rows > 0) {
                $row = $fallbackResult->fetch_assoc();
                $trainID = $row['trainNo'];
            } else {
                die("No trains available in database");
            }
        }
        
        // Check if the price column exists, use default if not
        if (!isset($row[$priceClass])) {
            $priceColumns = array_filter(array_keys($row), function($key) {
                return strpos($key, 'price') === 0;
            });
            if (!empty($priceColumns)) {
                $priceClass = reset($priceColumns);
            } else {
                $row[$priceClass] = 1000; // Default price
            }
        }
        
        $baseFare = $noOfPassengers * $row[$priceClass];
        $totalFare = $baseFare + 250;
        ?>

        <div class="col-sm-12 bookingTrain">
            <div class="col-sm-7">
                <div class="col-sm-12">
                    <div class="boxLeftBus">
                        <div class="col-sm-12 mode">Departure</div>

                        <div class="col-sm-4">
                            <div class="origin"><?php echo htmlspecialchars($origin); ?></div>
                            <div class="departs">Departs at: <?php echo htmlspecialchars($row["originTime"]); ?></div>
                        </div>

                        <div class="col-sm-4">
                            <div class="arrow"></div>
                        </div>

                        <div class="col-sm-4">
                            <div class="destination"><?php echo htmlspecialchars($destination); ?></div>
                            <div class="arrives">Arrives at: <?php echo htmlspecialchars($row["destinationTime"]); ?>
                            </div>
                        </div>

                        <div class="col-sm-3 borderRight">
                            <div class="class"><?php echo htmlspecialchars($date); ?></div>
                            <div class="classSubscript">Date of journey</div>
                        </div>

                        <div class="col-sm-5 borderRight">
                            <div class="operator"><?php echo htmlspecialchars($row["trainName"]); ?></div>
                            <div class="operatorSubscript">Name of the train</div>
                        </div>

                        <div class="col-sm-2 borderRight">
                            <div class="operator"><?php echo htmlspecialchars($class); ?></div>
                            <div class="operatorSubscript">Class</div>
                        </div>

                        <div class="col-sm-2">
                            <div class="adults"><?php echo $noOfPassengers; ?></div>
                            <div class="adultsSubscript">Passengers</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="col-sm-12">
                    <div class="boxRightBus">
                        <div class="col-sm-12 fareSummary">Fare Summary</div>

                        <div class="col-sm-8">
                            <div class="heading"><?php echo $noOfPassengers; ?> Passengers</div>
                            <div class="heading">Convenience Fee</div>
                        </div>

                        <div class="col-sm-4">
                            <div class="price"><span class="sansSerif">₹
                                </span><?php echo number_format($baseFare, 2); ?></div>
                            <div class="price"><span class="sansSerif">₹ </span>250.00</div>
                        </div>

                        <div class="col-sm-12">
                            <div class="calcBar"></div>
                        </div>

                        <div class="col-sm-8">
                            <div class="headingTotal">Total Fare</div>
                        </div>

                        <div class="col-sm-4">
                            <div class="priceTotal"><span class="sansSerif">₹
                                </span><?php echo number_format($totalFare, 2); ?></div>
                        </div>

                        <form method="POST">
                            <div class="bookingButton text-center">
                                <input type="submit" name="confirm_booking" class="confirmButton"
                                    value="Confirm Booking">
                            </div>

                            <input type="hidden" name="fareHidden" value="<?php echo $totalFare; ?>">
                            <input type="hidden" name="dateHidden" value="<?php echo htmlspecialchars($date); ?>">
                            <input type="hidden" name="dayHidden" value="<?php echo htmlspecialchars($day); ?>">
                            <input type="hidden" name="originHidden" value="<?php echo htmlspecialchars($origin); ?>">
                            <input type="hidden" name="destinationHidden"
                                value="<?php echo htmlspecialchars($destination); ?>">
                            <input type="hidden" name="classHidden" value="<?php echo htmlspecialchars($class); ?>">
                            <input type="hidden" name="noOfPassengersHidden" value="<?php echo $noOfPassengers; ?>">
                            <input type="hidden" name="modeHidden" value="train">
                            <input type="hidden" name="trainIDHidden" value="<?php echo htmlspecialchars($trainID); ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <div class="col-sm-12">
            <div class="alert alert-warning">
                <strong>Error:</strong> Invalid booking mode selected. Mode: <?php echo htmlspecialchars($mode); ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <div class="spacerLarge">.</div>

    <?php include("common/footer.php"); ?>

    <?php $conn->close(); ?>
</body>

</html>