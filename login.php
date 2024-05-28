<?php
session_start();
include "config.php";

if (isset($_SESSION["aadhar"]) && isset($_SESSION["phone"])) {
    // Redirect to dashboard if user is already logged in
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST["submit"])) {
    $aadhar = trim($_POST["aadhar"]);
    $phone = trim($_POST["phone"]);
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $secret_key = 'reCAPTCHA_SECRET_KEY';
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_response_data = file_get_contents($recaptcha_url . '?secret=' . $secret_key . '&response=' . $recaptcha_response);
    $recaptcha_data = json_decode($recaptcha_response_data);

    if ($recaptcha_data->success) {
        // Prepare and bind
        $stmt = $conn->prepare("SELECT * FROM users WHERE aadhar = ? AND phone = ?");
        $stmt->bind_param("ss", $aadhar, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION["aadhar"] = $aadhar;
            $_SESSION["phone"] = $phone;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Aadhar or Phone Number.";
        }
        $stmt->close();
    } else {
        $error = "reCAPTCHA verification failed. Please try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./public/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/log-in.css">
    <title>Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body onload="showModal()">
    <div class="left">
        <div class="logo">
            <img src="./public/logo.svg" alt="ECI Logo">
            <div class="logo-text">
                <h3>भारत निर्वाचन आयोग</h3>
                <h3>Election Commission of India</h3>
            </div>
        </div>
    </div>

    <div class="right">
        <form action="" method="post">
            <h2>Log in</h2>
            <p>Don't have an account? <a href="./sign-up.php">Sign Up</a></p>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <label for="aadhar">Aadhar Card Number</label>
            <input type="text" name="aadhar" id="aadhar" required>
            <br><br>
            
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" required>

            <div class="g-recaptcha" data-sitekey="reCAPTCHA_SITE_KEY"></div>

            <input type="submit" name="submit" value="Login">
        </form>
    </div>

    <div id="voteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Important Information   </h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>As this is a working prototype,</p>
                <p>please ensure that the original</p>
                <p><b>Aadhaar Card</b> and <b>Phone Number</b></p>
                <p>are not disclosed.</p>
            </div>
        </div>
    </div>

    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <script>
        document.getElementById('aadhar').addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '');
        });

        document.getElementById('phone').addEventListener('input', function (e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '');
        });

        // Show modal on page load
        function showModal() {
            document.getElementById('voteModal').style.display = 'block';
        }

        // Close modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('voteModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('voteModal')) {
                document.getElementById('voteModal').style.display = 'none';
            }
        }
    </script>
</body>
</html>


       
