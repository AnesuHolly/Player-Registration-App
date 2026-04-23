<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$error = null;
$success = null;

// Handle team addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_team'])) {
    $teamName = $_POST['team_name'];
    if (empty($teamName)) {
        $error = "Team name is required.";
    } else {
        $insertQuery = $conn->prepare("INSERT INTO teams (name) VALUES (?)");
        $insertQuery->bind_param("s", $teamName);
        if ($insertQuery->execute()) {
            $success = "Team added successfully.";
        } else {
            $error = "Failed to add team.";
        }
        $insertQuery->close();
    }
}

// Handle team deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteQuery = $conn->prepare("DELETE FROM teams WHERE team_id = ?");
    $deleteQuery->bind_param("i", $deleteId);
    if ($deleteQuery->execute()) {
        $success = "Team deleted successfully.";
    } else {
        $error = "Failed to delete team.";
    }
    $deleteQuery->close();
}

// Fetch all teams
$teamQuery = $conn->query("SELECT * FROM teams");
$teams = [];
while ($row = $teamQuery->fetch_assoc()) {
    $teams[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Teams</h1>
        </header>
        <main>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif ($success): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <form method="POST" action="manage_teams.php">
                <label for="team_name">Team Name:</label>
                <input type="text" id="team_name" name="team_name" required>
                <button type="submit" name="add_team" class="btn">Add Team</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Team ID</th>
                        <th>Team Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($team['team_id']); ?></td>
                            <td><?php echo htmlspecialchars($team['name']); ?></td>
                            <td>
                                <a href="manage_teams.php?delete_id=<?php echo htmlspecialchars($team['team_id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
        <footer>
            <a href="register.php" class="btn">Register</a>
            <a href="manage_players.php" class="btn">Manage Players</a>
            <a href="logout.php" class="btn">Logout</a>
            <p>&copy; 2024 South African Football Association</p>
        </footer>
    </div>
</body>
</html>
