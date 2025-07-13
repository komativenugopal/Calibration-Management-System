<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "project_database"; // ✅ Your DB name

$conn = new mysqli("localhost", "root", "", "project_database", 3307);


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
