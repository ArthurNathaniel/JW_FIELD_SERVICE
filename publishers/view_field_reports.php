<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

// Fetch field reports for the logged-in member
$sql = "SELECT id, name, phone_number, house_number, materials, report_date, created_at FROM field_reports WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Field Reports</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/view.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <h2>Your Field Reports</h2>
    </div>

    <?php if (empty($reports)): ?>
        <p>No reports found.</p>
    <?php else: ?>
        <table class="reports_table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>House Number</th>
                    <th>Materials</th>
                    <th>Report Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $index => $report): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($report['name']); ?></td>
                        <td><?php echo htmlspecialchars($report['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['house_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['materials']); ?></td>
                        <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                        <td>

                            <a href="delete_field_report.php?id=<?php echo $report['id']; ?>" onclick="return confirm('Are you sure you want to delete this report?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
