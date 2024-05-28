<?php
include "config.php";

session_start();

if (!isset($_SESSION["aadhar"]) || !isset($_SESSION["phone"])) {
    header("Location: index.php");
    exit();
}

$aadhar = trim($_SESSION["aadhar"]);
$phone = trim($_SESSION["phone"]);

$stmt = $conn->prepare("SELECT * FROM users WHERE aadhar = ? AND phone = ?");
$stmt->bind_param("ss", $aadhar, $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstname = $row["firstname"];
    $lastname = $row["lastname"];
    $constituency = $row["constituency"];
    $voted = $row["voted"];
}

$stmt->close();

if (!empty($constituency)) {
    $stmt = $conn->prepare("SELECT * FROM telangana_constituencies WHERE name = ?");
    $stmt->bind_param("s", $constituency);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $congress_candidate = $row["Congress_Candidate"] ?? '';
        $bjp_candidate = $row["BJP_Candidate"] ?? '';
        $brs_candidate = $row["BRS_Candidate"] ?? '';
        $reserved_for = $row["RESERVED_FOR"] ?? '';
    }

    $stmt->close();
}

$displayText = $constituency;
if (!empty($reserved_for)) {
    $displayText .= ' (' . $reserved_for . ')';
}

function vote($party, $conn, $constituency)
{
    $sql = "";
    switch ($party) {
        case 1:
            $sql = "UPDATE votes SET CONGRESS_VOTES = CONGRESS_VOTES + 1 WHERE name = ?";
            break;
        case 2:
            $sql = "UPDATE votes SET BJP_VOTES = BJP_VOTES + 1 WHERE name = ?";
            break;
        case 3:
            $sql = "UPDATE votes SET BRS_VOTES = BRS_VOTES + 1 WHERE name = ?";
            break;
        case 4:
            $sql = "UPDATE votes SET NOTA_VOTES = NOTA_VOTES + 1 WHERE name = ?";
            break;
    }

    if (!empty($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $constituency);
        if ($stmt->execute()) {
            // Mark user as voted
            $update_stmt = $conn->prepare("UPDATE users SET voted = 1 WHERE aadhar = ? AND phone = ?");
            $update_stmt->bind_param("ss", $_SESSION["aadhar"], $_SESSION["phone"]);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            echo "<script>console.log('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['party'])) {
    $party = intval($_POST['party']);
    vote($party, $conn, $constituency);
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh page to reflect changes
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./public/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="./css/dashboard.css">
    <title>Home</title>
</head>

<body>
    <header>
        <div class="logo">
            <img src="./public/logo.svg" alt="ECI Logo">
            <div class="logo-text">
                <h3>भारत निर्वाचन आयोग</h3>
                <h3>Election Commission of India</h3>
            </div>
        </div>
        <h3>Your Constituency: <?php echo htmlspecialchars($displayText); ?> </h3>
        <a href="./logout.php">
            <div class="logout">
                <p>Logout</p>
                <span class="material-symbols-outlined">logout</span>
            </div>
        </a>
    </header>

    <main>
        <div class="left">
            <h2>Personal Information</h2>
            <h3>First Name:</h3>
            <input type="text" value="<?php echo htmlspecialchars($firstname); ?>" readonly>
            <h3>Last Name:</h3>
            <input type="text" value="<?php echo htmlspecialchars($lastname); ?>" readonly>
            <h3>Aadhar Card Number:</h3>
            <input type="text" value="<?php echo htmlspecialchars($aadhar); ?>" readonly>
            <h3>Phone Number:</h3>
            <input type="text" value="<?php echo htmlspecialchars($phone); ?>" readonly>
        </div>

        <div class="right">
            <?php if (!$voted) : ?>
                <div class="vote-message">
                    <h3>Cast your vote now.</h3>
                </div>
            <?php endif; ?>

            <?php if ($voted) : ?>
                <div class="vote-message">
                    <h3>Your vote has been cast.</h3>
                </div>
            <?php endif; ?>

            <table border="1">
                <tr>
                    <th>S. No.</th>
                    <th>Candidate Name</th>
                    <th>Party Name</th>
                    <th>Symbol</th>
                    <th>Action</th>
                </tr>

                <?php $num = 1;
                if (!empty($congress_candidate)) : ?>
                    <tr>
                        <td><?php echo $num;
                            $num++; ?></td>
                        <td><?php echo htmlspecialchars($congress_candidate); ?></td>
                        <td>Congress Party</td>
                        <td><img src="./public/party_symbols/congress.png" height="60px" width="60px"></td>
                        <td>
                            <form method="post" class="vote-form">
                                <input type="hidden" name="party" value="1">
                                <button type="button" class="vote <?php echo $voted ? 'disabled' : ''; ?>" <?php echo $voted ? 'disabled' : ''; ?> data-candidate="<?php echo htmlspecialchars($congress_candidate); ?>" data-party="congress" data-party-name="Congress Party">
                                    <p>Vote</p>
                                    <span class="material-symbols-outlined">touch_app</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($bjp_candidate)) : ?>
                    <tr>
                        <td><?php echo $num;
                            $num++; ?></td>
                        <td><?php echo htmlspecialchars($bjp_candidate); ?></td>
                        <td>Bharatiya Janata Party (BJP)</td>
                        <td><img src="./public/party_symbols/bjp.png" height="60px" width="60px"></td>
                        <td>
                            <form method="post" class="vote-form">
                                <input type="hidden" name="party" value="2">
                                <button type="button" class="vote <?php echo $voted ? 'disabled' : ''; ?>" <?php echo $voted ? 'disabled' : ''; ?> data-candidate="<?php echo htmlspecialchars($bjp_candidate); ?>" data-party="bjp" data-party-name="Bharatiya Janata Party (BJP)">
                                    <p>Vote</p>
                                    <span class="material-symbols-outlined">touch_app</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($brs_candidate)) : ?>
                    <tr>
                        <td><?php echo $num;
                            $num++; ?></td>
                        <td><?php echo htmlspecialchars($brs_candidate); ?></td>
                        <td>Bharat Rashtra Samithi (BRS)</td>
                        <td><img src="./public/party_symbols/brs.png" height="60px" width="60px"></td>
                        <td>
                            <form method="post" class="vote-form">
                                <input type="hidden" name="party" value="3">
                                <button type="button" class="vote <?php echo $voted ? 'disabled' : ''; ?>" <?php echo $voted ? 'disabled' : ''; ?> data-candidate="<?php echo htmlspecialchars($brs_candidate); ?>" data-party="brs" data-party-name="Bharat Rashtra Samithi (BRS)">
                                    <p>Vote</p>
                                    <span class="material-symbols-outlined">touch_app</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td><?php echo $num;
                        $num++; ?></td>
                    <td>NOTA</td>
                    <td>None Of The Above</td>
                    <td><img src="./public/party_symbols/nota.png" height="60px" width="60px"></td>
                    <td>
                        <form method="post" class="vote-form">
                            <input type="hidden" name="party" value="4">
                            <button type="button" class="vote <?php echo $voted ? 'disabled' : ''; ?>" <?php echo $voted ? 'disabled' : ''; ?> data-candidate="NOTA" data-party="nota" data-party-name="None Of The Above">
                                <p>Vote</p>
                                <span class="material-symbols-outlined">touch_app</span>
                            </button>
                        </form>
                    </td>
                </tr>

            </table>
        </div>
    </main>

    <!-- The Modal -->
    <div id="voteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Your Vote</h2>
            </div>
            <div class="modal-body">
                <p class="normal-txt">Are you sure you want to vote for</p>
                <img id="party-symbol" src="" height="60px" width="60px">
                <p><b><span id="candidate-name"></span></b></p>
                <p class="normal-txt">from</p>
                <p><b><span id="party-name"></span></b>?</p>
            </div>
            <div class="modal-footer">
                <button id="cancelVote">Cancel</button>
                <button id="confirmVote">Vote</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById("voteModal");
            const candidateNameSpan = document.getElementById("candidate-name");
            const partyNameSpan = document.getElementById("party-name");
            const partySymbolImg = document.getElementById("party-symbol");
            let currentForm;

            document.querySelectorAll('.vote').forEach(button => {
                button.addEventListener('click', function() {
                    const candidateName = this.getAttribute('data-candidate');
                    const partyName = this.getAttribute('data-party');
                    const partyFullName = this.getAttribute('data-party-name');
                    candidateNameSpan.textContent = candidateName;
                    partyNameSpan.textContent = partyFullName;

                    // Set the party symbol image source
                    const partySymbolSrc = `./public/party_symbols/${partyName}.png`;
                    partySymbolImg.src = partySymbolSrc;

                    currentForm = this.closest('form');
                    modal.style.display = "block";
                });
            });

            // Close modal on click of cancel button
            document.getElementById('cancelVote').addEventListener('click', function() {
                modal.style.display = "none";
            });

            // Submit the form on click of confirm button
            document.getElementById('confirmVote').addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
            });

            // Close modal on clicking outside the modal content
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
</body>

</html>
