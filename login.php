<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $query->bind_param("ss", $username, $password);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header("Location: register.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAFA Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
             /* addded background image */
        background-image: url('images/ea-sports-fc-24-football-stadium-4k-wallpaper-uhdpaper.com-129@1@m.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>SAFA Login</h1>
        </header>
        <main>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit" class="btn">Login</button>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </form>
        </main>
        <footer>
            <a href="index.html">Home</a>
            <p>&copy; 2024 South African Football Association</p>
        </footer>
    </div>
</body>
</html>
