<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login_member.php");
    exit();
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $house_number = $_POST['house_number'];
    $materials = isset($_POST['materials']) ? implode(', ', $_POST['materials']) : '';
    $report_date = $_POST['report_date'];

    $sql = "INSERT INTO field_reports (member_id, name, phone_number, house_number, materials, report_date) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $_SESSION['member_id'], $name, $phone_number, $house_number, $materials, $report_date);

    if ($stmt->execute()) {
        $success = "Report added successfully!";
    } else {
        $error = "Failed to add report. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Field Report</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <div class="logo"></div>
        <h2>Add Field Report</h2>
    </div>

    <?php if ($success) { echo "<p class='success'>$success</p>"; } ?>
    <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>

    <form action="add_field_report.php" method="POST">
        <div class="forms">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="forms">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" required>
        </div>
        <div class="forms">
            <label for="house_number">House Number</label>
            <input type="text" id="house_number" name="house_number" required>
        </div>
        <div class="forms">
            <label for="materials">Select Materials (Hold Ctrl to select multiple)</label>
            <select id="materials" name="materials[]" multiple required>
                <option value="Magazine">Magazine</option>
                <option value="Video">Video</option>
                <option value="Audio">Audio</option>
            </select>
        </div>
        <div class="forms">
            <label for="report_date">Report Date</label>
            <input type="date" id="report_date" name="report_date" required>
        </div>
        <div class="forms">
            <button type="submit">Submit Report</button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
