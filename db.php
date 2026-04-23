<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname_safa = "safadb";
$dbname_homeaffairs = "homeaffairsdb";

// Create connection to safadb
$conn = new mysqli($servername, $username, $password, $dbname_safa);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create connection to homeaffairsdb
$conn_homeaffairs = new mysqli($servername, $username, $password, $dbname_homeaffairs);

// Check connection
if ($conn_homeaffairs->connect_error) {
    die("Connection failed: " . $conn_homeaffairs->connect_error);
}
?>
