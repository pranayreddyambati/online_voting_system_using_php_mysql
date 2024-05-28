<?php
include "config.php";

// Fetch constituencies from the database
$sql = "SELECT ID, NAME, RESERVED_FOR FROM telangana_constituencies";
$result = $conn->query($sql);

$constituencies = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $constituencies[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $aadhar = $_POST["aadhar"];
    $phone = $_POST["phone"];
    $constituency = $_POST["constituency"];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $secret_key = 'reCAPTCHA_SECRET_KEY';
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_response_data = file_get_contents($recaptcha_url . '?secret=' . $secret_key . '&response=' . $recaptcha_response);
    $recaptcha_data = json_decode($recaptcha_response_data);

    if ($recaptcha_data->success) {
        // Server-side validation
        if (preg_match("/^[a-zA-Z ]+$/", $firstname) && preg_match("/^[a-zA-Z ]+$/", $lastname) &&
            preg_match("/^[0-9]{4}\s?[0-9]{4}\s?[0-9]{4}$/", $aadhar) &&
            preg_match("/^[0-9]{3}\s?[0-9]{3}\s?[0-9]{4}$/", $phone)) {

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, aadhar, phone, constituency) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstname, $lastname, $aadhar, $phone, $constituency);

            if ($stmt->execute()) {
                echo "<script>alert('User registered successfully!'); window.location.href = 'login.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Invalid input data. Please check your inputs and try again.');</script>";
        }
    } else {
        echo "<script>alert('reCAPTCHA verification failed. Please try again.');</script>";
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
    <link rel="stylesheet" href="./css/signup.css">
    <title>Sign Up</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body onload="showModal()">
    <div class="left">
        <div class="logo">
            <img src="./public/logo.svg" alt="ECI Logo">
            <div class="logo-text">
                <h3>Join the Election Commission of India</h3>
                <p>Be a part of the largest democratic process in the world.</p>
            </div>
        </div>
    </div>

    <div class="right">
        <form action="" method="post">
            <h2>Sign Up</h2>
            <p>Already have an account? <a href="./index.php">Login</a></p>

            <div class="name-fields">
                <div class="field">
                    <label for="firstname">First Name</label> 
                    <input type="text" name="firstname" id="firstname" required>
                </div>
                <div class="field">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" required>
                </div>
            </div>
            
            <label for="aadhar">Aadhar Card Number</label>
            <input type="text" name="aadhar" id="aadhar" maxlength="14" pattern="[0-9]{4}[\s-]?[0-9]{4}[\s-]?[0-9]{4}" required>
            
            <div class="name-fields">
                <div class="field">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" maxlength="12" pattern="[0-9]{3}[\s]?[0-9]{3}[\s]?[0-9]{4}" required>
                </div>
                <div class="field">
                    <label for="constituency">Constituency</label>
                    <select name="constituency" id="constituency" required>
                        <option value="">Select Constituency</option>
                        <?php
                        foreach ($constituencies as $constituency) {
                            $displayText = $constituency['NAME'];
                            if (!empty($constituency['RESERVED_FOR'])) {
                                $displayText .= ' (' . $constituency['RESERVED_FOR'] . ')';
                            }
                            echo '<option value="' . $constituency['NAME'] . '">' . $displayText . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="g-recaptcha" data-sitekey="reCAPTCHA_SITE_KEY"></div>
            
            <input type="submit" name="submit" value="Sign Up">
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
        // Format Aadhar and Phone number inputs
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
