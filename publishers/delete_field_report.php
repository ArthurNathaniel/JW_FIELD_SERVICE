<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$report_id = $_GET['id'];

// Delete the report
$sql = "DELETE FROM field_reports WHERE id = ? AND member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $report_id, $member_id);

if ($stmt->execute()) {
    header("Location: view_field_reports.php");
    exit();
} else {
    die("Failed to delete the report. Please try again.");
}

$stmt->close();
$conn->close();
?>
