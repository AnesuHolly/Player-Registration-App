<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_GET['id'] ?? null;
$error = null;
$teams = [];

if (!$id) {
    $error = "Invalid access.";
} else {
    // Fetch player details with team name
    $query = $conn->prepare("
        SELECT players.*, teams.name AS team_name 
        FROM players 
        LEFT JOIN teams ON players.team_id = teams.team_id 
        WHERE players.id = ?
    ");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows == 1) {
        $player = $result->fetch_assoc();
        // Decode photograph data
        $player['photograph'] = base64_encode($player['photograph']);
    } else {
        $error = "Player not found.";
    }
    $query->close();
}

// Fetch available teams
$teamQuery = $conn->query("SELECT * FROM teams");
while ($row = $teamQuery->fetch_assoc()) {
    $teams[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teamId = $_POST['teamId'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];

    // Add error checking for required fields
    if (empty($teamId) || empty($contactNumber) || empty($email)) {
        $error = "All fields are required.";
    } else {
        $updateQuery = $conn->prepare("UPDATE players SET team_id = ?, contact_number = ?, email = ? WHERE id = ?");
        if ($updateQuery === false) {
            die('Error preparing query: ' . $conn->error);
        }
        $bindResult = $updateQuery->bind_param("issi", $teamId, $contactNumber, $email, $id);
        if ($bindResult === false) {
            die('Error binding parameters: ' . $updateQuery->error);
        }
        $executeResult = $updateQuery->execute();
        if ($executeResult === false) {
            die('Error executing query: ' . $updateQuery->error);
        }

        header("Location: manage_players.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Player</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .player-photo {
            max-width: 150px;
            max-height: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Edit Player Details</h1>
        </header>
        <main>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php else: ?>
                <form method="POST" action="">
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($player['full_name']); ?>" disabled>

                    <label for="idNumber">ID Number:</label>
                    <input type="text" id="idNumber" name="idNumber" value="<?php echo htmlspecialchars($player['id_number']); ?>" disabled>

                    <label for="dob">Date of Birth:</label>
                    <input type="text" id="dob" name="dob" value="<?php echo htmlspecialchars($player['dob']); ?>" disabled>

                    <label for="placeOfBirth">Place of Birth:</label>
                    <input type="text" id="placeOfBirth" name="placeOfBirth" value="<?php echo htmlspecialchars($player['place_of_birth']); ?>" disabled>

                    <label for="gender">Gender:</label>
                    <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($player['gender']); ?>" disabled>

                    <label for="photograph">Photograph:</label>
                    <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($player['photograph']); ?>" alt="Player Photograph" class="player-photo" />

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
                    <input type="text" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($player['contact_number']); ?>" required pattern="\d{10}" title="Enter a valid 10-digit contact number">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($player['email']); ?>" required>

                    <button type="submit" class="btn">Update Player</button>
                </form>
            <?php endif; ?>
        </main>
        <footer>
            <a href="manage_players.php" class="btn">Back to Players</a>
            <a href="logout.php" class="btn">Logout</a>
            <p>&copy; 2024 South African Football Association</p>
        </footer>
    </div>
</body>
</html>
