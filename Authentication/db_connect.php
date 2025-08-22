<?php
$host = "localhost"; 
$user = "root";       // default XAMPP user
$pass = "";           // default XAMPP password (empty unless changed)
$dbname = "ams_db";   // your DB name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Database Connection failed: " . $conn->connect_error);
}
?>
