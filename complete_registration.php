<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$idNumber = $_GET['idNumber'] ?? null;
$error = null;
$teams = [];

if (!$idNumber) {
    $error = "Invalid access.";
} else {
    // Fetch player details to pre-fill the form
    $query = $conn->prepare("SELECT * FROM players WHERE id_number = ?");
    $query->bind_param("s", $idNumber);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows == 1) {
        $player = $result->fetch_assoc();
    } else {
        $error = "Player not found.";
    }
    $query->close();

    // Fetch available teams
    $teamQuery = $conn->query("SELECT * FROM teams");
    while ($row = $teamQuery->fetch_assoc()) {
        $teams[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teamId = $_POST['teamId'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];

    $updateQuery = $conn->prepare("UPDATE players SET team_id = ?, contact_number = ?, email = ? WHERE id_number = ?");
    $updateQuery->bind_param("isss", $teamId, $contactNumber, $email, $idNumber);
    if ($updateQuery->execute()) {
        header("Location: success.php");
        exit();
    } else {
        $error = "Failed to update player details.";
    }
    $updateQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Complete Player Registration</h1>
        </header>
        <main>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php else: ?>
                <form method="POST" action="complete_registration.php?idNumber=<?php echo htmlspecialchars($idNumber); ?>">
                    <p>Full Name: <?php echo htmlspecialchars($player['full_name']); ?></p>
                    <p>Surname: <?php echo htmlspecialchars($player['surname']); ?></p>
                    <p>Date of Birth: <?php echo htmlspecialchars($player['dob']); ?></p>
                    <p>Place of Birth: <?php echo htmlspecialchars($player['place_of_birth']); ?></p>
                    <p>Gender: <?php echo htmlspecialchars($player['gender']); ?></p>

                    <label for="teamId">Team Name:</label>
                    <select id="teamId" name="teamId" required>
                        <option value="">Select Team</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo htmlspecialchars($team['team_id']); ?>" <?php echo ($player['team_id'] == $team['team_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($team['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="contactNumber">Contact Number:</label>
                    <input type="text" id="contactNumber" name="contactNumber" required pattern="\d{10}" title="Enter a valid 10-digit contact number" value="<?php echo htmlspecialchars($player['contact_number']); ?>">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($player['email']); ?>">

                    <button type="submit" class="btn">Confirm Registration</button>
                </form>
            <?php endif; ?>
        </main>
        <footer>
            <a href="logout.php" class="btn">Logout</a>
            <p>&copy; 2024 South African Football Association</p>
        </footer>
    </div>
</body>
</html>
