<?php

include "config.php";

$sql = "SELECT ID, NAME, RESERVED_FOR FROM telangana_constituencies";
$result = $conn->query($sql);

$constituency = "";
$constituencies = [];

$congress_votes = $bjp_votes = $brs_votes = $nota_votes = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $constituencies[] = $row;
    }
}

if (isset($_POST["constituency"])) {
    $constituency = $_POST["constituency"];
}

if (!empty($constituency)) {
    $stmt = $conn->prepare("SELECT * FROM telangana_constituencies WHERE name = ?");
    $stmt->bind_param("s", $constituency);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $congress_candidate = !empty($row["Congress_Candidate"]) ? $row["Congress_Candidate"] : '';
        $bjp_candidate = !empty($row["BJP_Candidate"]) ? $row["BJP_Candidate"] : '';
        $brs_candidate = !empty($row["BRS_Candidate"]) ? $row["BRS_Candidate"] : '';
        $reserved_for = !empty($row["RESERVED_FOR"]) ? '(' . $row["RESERVED_FOR"] . ')' : '';
    }

    $display = $constituency . " " . $reserved_for;

    $stmt->close();
}

if (!empty($constituency)) {
    $stmt = $conn->prepare("SELECT * FROM votes WHERE name = ?");
    $stmt->bind_param("s", $constituency);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $congress_votes = !empty($row["CONGRESS_VOTES"]) ? $row["CONGRESS_VOTES"] : 0;
        $bjp_votes = !empty($row["BJP_VOTES"]) ? $row["BJP_VOTES"] : 0;
        $brs_votes = !empty($row["BRS_VOTES"]) ? $row["BRS_VOTES"] : 0;
        $nota_votes = !empty($row["NOTA_VOTES"]) ? $row["NOTA_VOTES"] : 0;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./public/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="./css/result.css">
    <title>Results</title>
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
        <h3>General Election Results - 2024</h3>

        <button id="refreshButton">
            Refresh
            <span class="material-symbols-outlined">
                refresh
            </span>
        </button>
    </header>

    <main>
        <div class="left">
            <form action="" method="post" id="constituencyForm">
                <select name="constituency" id="constituencySelect">
                    <option value="" selected disabled>Select Constituency</option>
                    <?php
                    foreach ($constituencies as $constituency_selected) {
                        $displayText = $constituency_selected['NAME'];
                        if (!empty($constituency_selected['RESERVED_FOR'])) {
                            $displayText .= ' (' . $constituency_selected['RESERVED_FOR'] . ')';
                        }
                        echo '<option value="' . $constituency_selected['NAME'] . '">' . $displayText . '</option>';
                    }
                    ?>
                </select>
            </form>
        </div>

        <div class="right">
            <?php if (!empty($constituency)) : ?>
                <?php echo "<h4>Constituency Selected: $display</h4>"; ?>
                <table border="1">
                    <tbody>
                        <tr>
                            <th>S.No.</th>
                            <th>Candidate Name</th>
                            <th>Party Name</th>
                            <th>Symbol</th>
                            <th>Votes</th>
                        </tr>

                        <?php $num = 1;
                        if (!empty($congress_candidate)) : ?>
                            <tr>
                                <td><?php echo $num;
                                    $num++; ?></td>
                                <td><?php echo htmlspecialchars($congress_candidate); ?></td>
                                <td>Congress Party</td>
                                <td><img src="./public/party_symbols/congress.png" height="60px" width="60px"></td>
                                <td><?php echo $congress_votes; ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($bjp_candidate)) : ?>
                            <tr>
                                <td><?php echo $num;
                                    $num++; ?></td>
                                <td><?php echo htmlspecialchars($bjp_candidate); ?></td>
                                <td>Bharatiya Janata Party (BJP)</td>
                                <td><img src="./public/party_symbols/bjp.png" height="60px" width="60px"></td>
                                <td><?php echo $bjp_votes; ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($brs_candidate)) : ?>
                            <tr>
                                <td><?php echo $num;
                                    $num++; ?></td>
                                <td><?php echo htmlspecialchars($brs_candidate); ?></td>
                                <td>Bharat Rashtra Samithi (BRS)</td>
                                <td><img src="./public/party_symbols/brs.png" height="60px" width="60px"></td>
                                <td><?php echo $brs_votes; ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td><?php echo $num;
                                $num++; ?></td>
                            <td>NOTA</td>
                            <td>None Of The Above</td>
                            <td><img src="./public/party_symbols/nota.png" height="60px" width="60px"></td>
                            <td><?php echo $nota_votes; ?></td>
                        </tr>

                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#constituencySelect').change(function() {
                $('#constituencyForm').submit();
            });

            $('#refreshButton').click(function() {
                location.reload();
            });
        });
    </script>

</body>

</html>