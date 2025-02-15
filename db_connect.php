<?php
$host = "localhost";
$username = "root"; // Your database username
$password = "";     // Your database password
$database = "recipe_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>