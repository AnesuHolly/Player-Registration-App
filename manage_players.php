<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all registered players with their team names
$query = $conn->query("
    SELECT players.*, teams.name AS team_name
    FROM players
    LEFT JOIN teams ON players.team_id = teams.team_id
");
$players = [];
while ($row = $query->fetch_assoc()) {
    $players[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Players</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Players</h1>
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Full Name</th>
                        <th>Team</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($player['id_number']); ?></td>
                            <td><?php echo htmlspecialchars($player['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($player['team_name'] ?? 'No Team'); ?></td>
                            <td><?php echo htmlspecialchars($player['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($player['email']); ?></td>
                            <td><?php echo htmlspecialchars($player['status']); ?></td>
                            <td><a href="edit_player.php?id=<?php echo htmlspecialchars($player['id']); ?>" class="btn">Manage Player</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
        <footer>
        <footer>
            <a href="register.php" class="btn">Register</a>
            <a href="manage_teams.php" class="btn">Manage Teams</a>
            <a href="logout.php" class="btn">Logout</a>
            <p>&copy; 2026 South African Football Association</p>
        </footer>
        </footer>
    </div>
</body>
</html>
