<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$db_servername = "localhost";
$db_username = "root"; 
$db_password = "";
$db_name = "daily_db";

try {
    $conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if (!$conn->set_charset("utf8")) {
        throw new Exception("Error setting charset: " . $conn->error);
    }

} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>