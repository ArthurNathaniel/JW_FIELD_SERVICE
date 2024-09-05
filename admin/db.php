<?php
// $servername = "localhost";
// $username = "root"; // Default username for XAMPP
// $password = ""; // Default password for XAMPP
// $dbname = "jw_field_service";


$servername = "longwellconnect.com";
$username = "u500921674_jw"; // Default username for XAMPP
$password = "OnGod@123"; // Default password for XAMPP
$dbname = "u500921674_jw";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
