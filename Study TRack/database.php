<?php
$dbHost = getenv("STUDYTRACK_DB_HOST") ?: "localhost";
$dbUser = getenv("STUDYTRACK_DB_USER") ?: "root";
$dbPassword = getenv("STUDYTRACK_DB_PASSWORD") ?: "";
$dbName = getenv("STUDYTRACK_DB_NAME") ?: "studytrack";
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>