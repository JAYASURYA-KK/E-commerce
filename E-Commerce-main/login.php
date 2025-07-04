<?php  session_start();
 include_once 'includes/config.php';
//  all functions
require_once 'functions/functions.php';

 //run whenever this file is used no need of isset or any condition to get website image footer etc
 $sql5 ="SELECT * FROM  settings;";
 $result5 = $conn->query($sql5);
 $row5 = $result5->fetch_assoc();
 $_SESSION['web-name'] = $row5['website_name'];
 $_SESSION['web-img'] = $row5['website_logo'];
 $_SESSION['web-footer'] = $row5['website_footer'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <title>Login(USER)</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .voice-btn {
        background: none;
        border: none;
        padding: 8px;
        cursor: pointer;
        color: #666;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .voice-btn:hover {
        color: #333;
        background-color: #f0f0f0;
    }

    .voice-btn.listening {
        color: #ff6b6b;
        background-color: #ffe6e6;
    }

    .search-form {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .search-field {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    body {
        display: flex;
        flex-direction: column;
        height: 100vh;
        justify-content: center;
        align-items: center;
    }

    form {
        border: 1px solid red;
        width: 400px;
        padding: 25px;
        border-radius: 10px;
    }

    .logo-box {
        padding: 10px;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    #signup-btn {
        text-decoration: none;
        color: white;
    }
    </style>
</head>

<body>

    <?php 
     if( !( isset( $_SESSION['id']))){
     ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?> " method="post">

        <div class="row mb-3">
            <!-- <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label> -->
            <div class="col-sm-12">
                <input id="inputEmail" name="email" type="email" class="form-control" placeholder="Email" />
            </div>
        </div>
        <div class="row mb-3">
            <!-- <label for="inputPassword3" class="col-sm-2 col-form-label"
          >Password</label
        > -->
            <div class="col-sm-12">
                <input id="inputPassword" name="pwd" type="password" class="form-control" placeholder="Password" />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-10">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck1" />
                    <label class="form-check-label" for="gridCheck1">
                        Remeber Me
                    </label>
                </div>
            </div>
        </div>
        <div style="float: right">


            <button type="submit" class="btn btn-primary" name="login">
                Sign in
            </button>
        </div>
    </form>

    <?php }?>


    <?php
 //1st step(i.e connection) done through config file
if(isset($_POST['login'])){

    if(empty($_POST['email'])){
           echo "<h4 id='error_login'>Enter email</h4>";
    }

    if(empty($_POST['pwd'])){
        echo "<h4 id='error_login'>Enter password</h4>";
 }

$email = mysqli_real_escape_string($conn,$_POST['email']);
$password =mysqli_real_escape_string($conn,$_POST['pwd']);

$sql ="SELECT * FROM  customer WHERE customer_email='{$email}';";
$result = $conn->query($sql);

if($result->num_rows==1){ //if any one data found go inside it
    $row = $result->fetch_assoc();
    if($password == $row['customer_pwd']){

    //session will be created only if users email and passwords matched
	session_start();
	$_SESSION['id'] = $row['customer_id'];
	$_SESSION['customer_role'] = $row['customer_role'];

    header("location:profile.php?id={$_SESSION['id']}");
            // put exit after a redirect as header() does not stop execution
            exit;}else{
                echo "<h4 id='error_login'>Incorrect password</h4>";//as user get inside if statem if userEmail matched
            }


}else{
    if($_POST['email']){ //it means it will run if email field is filled
    echo "<h4 id='error_login'>(unavailable) please signup first</h4>";
    }
}
}//end of 1st ifstatement

?>


</body>

</html>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const voiceBtn = document.getElementById('voice-btn');
    const searchField = document.getElementById('search-field');

    if (!voiceBtn || !searchField) {
        console.error('Voice button or search field not found');
        return;
    }

    // Check if browser supports speech recognition
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        voiceBtn.style.display = 'none';
        console.log('Speech recognition not supported');
        return;
    }

    voiceBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();

        // Configure recognition
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';
        recognition.maxAlternatives = 1;

        // Get the icon element
        const voiceIcon = this.querySelector('ion-icon');

        // Start recognition
        try {
            recognition.start();

            // Visual feedback - listening state
            this.classList.add('listening');
            if (voiceIcon) {
                voiceIcon.setAttribute('name', 'radio-button-on');
            }

            console.log('Speech recognition started');

        } catch (error) {
            console.error('Error starting recognition:', error);
            alert('Error starting speech recognition. Please try again.');
            return;
        }

        // Handle successful recognition
        recognition.onresult = function(event) {
            console.log('Recognition result received');

            if (event.results && event.results.length > 0) {
                const transcript = event.results[0][0].transcript;
                console.log('Transcript:', transcript);
                searchField.value = transcript;
                searchField.focus();
            }
        };

        // Handle recognition end
        recognition.onend = function() {
            console.log('Speech recognition ended');

            // Reset visual state
            voiceBtn.classList.remove('listening');
            if (voiceIcon) {
                voiceIcon.setAttribute('name', 'mic-outline');
            }
        };

        // Handle errors
        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);

            // Reset visual state
            voiceBtn.classList.remove('listening');
            if (voiceIcon) {
                voiceIcon.setAttribute('name', 'mic-outline');
            }

            // Show user-friendly error messages
            let errorMessage = 'Speech recognition error. ';
            switch (event.error) {
                case 'no-speech':
                    errorMessage += 'No speech detected. Please try again.';
                    break;
                case 'audio-capture':
                    errorMessage += 'Microphone not accessible. Please check permissions.';
                    break;
                case 'not-allowed':
                    errorMessage += 'Microphone permission denied. Please allow microphone access.';
                    break;
                case 'network':
                    errorMessage += 'Network error. Please check your connection.';
                    break;
                default:
                    errorMessage += 'Please try again.';
            }

            alert(errorMessage);
        };

        // Handle start event
        recognition.onstart = function() {
            console.log('Speech recognition started successfully');
        };
    });
});
</script>