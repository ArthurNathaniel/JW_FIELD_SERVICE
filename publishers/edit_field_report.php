<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

// Get the report ID from the URL
if (!isset($_GET['id'])) {
    header("Location: view_field_reports.php");
    exit();
}

$report_id = $_GET['id'];

// Fetch the report details to be edited
$sql = "SELECT name, phone_number, house_number, materials, report_date FROM field_reports WHERE id = ? AND member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $report_id, $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No report found for this ID and member
    header("Location: view_field_reports.php");
    exit();
}

$report = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating the report
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $house_number = htmlspecialchars($_POST['house_number']);
    $materials = htmlspecialchars($_POST['materials']);
    $report_date = htmlspecialchars($_POST['report_date']);

    // Update the report in the database
    $sql = "UPDATE field_reports SET name = ?, phone_number = ?, house_number = ?, materials = ?, report_date = ? WHERE id = ? AND member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $name, $phone_number, $house_number, $materials, $report_date, $report_id, $member_id);

    if ($stmt->execute()) {
        // Redirect back to the view reports page after successful update
        header("Location: view_field_reports.php?success=1");
        exit();
    } else {
        $error = "Failed to update the report. Please try again.";
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
    <title>Edit Field Report</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/view.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <h2>Edit Field Report</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($report['name']); ?>" required>
            </div>
            <div>
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($report['phone_number']); ?>" required>
            </div>
            <div>
                <label for="house_number">House Number</label>
                <input type="text" id="house_number" name="house_number" value="<?php echo htmlspecialchars($report['house_number']); ?>" required>
            </div>
            <div>
                <label for="materials">Materials</label>
                <textarea id="materials" name="materials" required><?php echo htmlspecialchars($report['materials']); ?></textarea>
            </div>
            <div>
                <label for="report_date">Report Date</label>
                <input type="date" id="report_date" name="report_date" value="<?php echo htmlspecialchars($report['report_date']); ?>" required>
            </div>
            <div>
                <button type="submit">Update Report</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
