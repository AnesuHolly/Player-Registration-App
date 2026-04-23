<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$details = null;
$error = null;

function isValidSouthAfricanID($id) {
    return preg_match('/^\d{13}$/', $id);
}

if (isset($_GET['idNumber'])) {
    $idNumber = $_GET['idNumber'];

    if (isValidSouthAfricanID($idNumber)) {
        $query = $conn_homeaffairs->prepare("SELECT full_name, surname, dob, place_of_birth, gender, photograph FROM homeaffairs WHERE id_number = ?");
        
        if ($query) {
            $query->bind_param("s", $idNumber);
            $query->execute();
            $result = $query->get_result();
            if ($result->num_rows > 0) {
                $details = $result->fetch_assoc();
            } else {
                $error = "ID not found";
            }
            $query->close();
        } else {
            $error = "Error preparing statement: " . $conn_homeaffairs->error;
        }
    } else {
        $error = "Invalid South African ID number format";
    }
}

if (isset($_POST['register'])) {
    $idNumber = $_POST['idNumber'];
    $fullName = $_POST['fullName'];
    $surname = $_POST['surname'];
    $dob = $_POST['dob'];
    $placeOfBirth = $_POST['placeOfBirth'];
    $gender = $_POST['gender'];
    $photograph = $_POST['photograph'];
    
    $checkQuery = $conn->prepare("SELECT * FROM players WHERE id_number = ?");
    $checkQuery->bind_param("s", $idNumber);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    
    if ($result->num_rows == 0) {
        $insertQuery = $conn->prepare("INSERT INTO players (id_number, full_name, surname, dob, place_of_birth, gender, photograph) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("sssssss", $idNumber, $fullName, $surname, $dob, $placeOfBirth, $gender, $photograph);
        $insertQuery->execute();
        $insertQuery->close();
        header("Location: complete_registration.php?idNumber=" . $idNumber);
        exit();
    } else {
        $error = "Player with this ID number is already registered.";
    }
    $checkQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Player</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function clearForm() {
            document.getElementById('idNumber').value = '';
            document.getElementById('playerDetails').innerHTML = '';
            document.getElementById('registerButton').style.display = 'none';
        }
    </script>
    <style>
        img.player-photo {
            width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Register a Player</h1>
        </header>
        <main>
            <form method="GET" action="register.php">
                <label for="idNumber">ID Number:</label>
                <input type="text" id="idNumber" name="idNumber" required pattern="\d{13}" title="Enter a valid 13-digit South African ID number">
                <button type="submit" class="btn">Fetch Details</button>
                <button type="button" class="btn" onclick="clearForm()">Clear</button>
            </form>

            <?php if ($details): ?>
            <div id="playerDetails">
                <p>Full Name: <?php echo htmlspecialchars($details['full_name']); ?></p>
                <p>Surname: <?php echo htmlspecialchars($details['surname']); ?></p>
                <p>Date of Birth: <?php echo htmlspecialchars($details['dob']); ?></p>
                <p>Place of Birth: <?php echo htmlspecialchars($details['place_of_birth']); ?></p>
                <p>Gender: <?php echo htmlspecialchars($details['gender']); ?></p>
                <p>Photograph: <img src="data:image/jpeg;base64,<?php echo base64_encode($details['photograph']); ?>" alt="Player Photograph" class="player-photo"/></p>
            </div>

            <form method="POST" action="register.php">
                <input type="hidden" name="idNumber" value="<?php echo htmlspecialchars($idNumber); ?>">
                <input type="hidden" name="fullName" value="<?php echo htmlspecialchars($details['full_name']); ?>">
                <input type="hidden" name="surname" value="<?php echo htmlspecialchars($details['surname']); ?>">
                <input type="hidden" name="dob" value="<?php echo htmlspecialchars($details['dob']); ?>">
                <input type="hidden" name="placeOfBirth" value="<?php echo htmlspecialchars($details['place_of_birth']); ?>">
                <input type="hidden" name="gender" value="<?php echo htmlspecialchars($details['gender']); ?>">
                <input type="hidden" name="photograph" value="<?php echo base64_encode($details['photograph']); ?>">
                <button type="submit" name="register" class="btn" id="registerButton">Register Player</button>
            </form>
            <?php elseif ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </main>
        <footer>
            <a href="manage_players.php" class="btn">Manage Players</a>
            <a href="manage_teams.php" class="btn">Manage Teams</a>
            <a href="logout.php" class="btn">Logout</a>
            <p>&copy; 2024 South African Football Association</p>
        </footer>
    </div>
</body>
</html>
