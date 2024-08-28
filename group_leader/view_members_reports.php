<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['group_leader_id'])) {
    header("Location: login.php");
    exit();
}

$group_leader_id = $_SESSION['group_leader_id'];
$success = "";
$error = "";

// Fetch members' reports
$sql = "
    SELECT fr.id AS report_id, m.name AS member_name, fr.name, fr.phone_number, fr.house_number, fr.materials, fr.report_date 
    FROM field_reports fr
    JOIN members m ON fr.member_id = m.id
    WHERE m.group_leader_id = ?
    ORDER BY fr.report_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_leader_id);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members' Field Reports</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <h2>Members' Field Reports</h2>
    </div>

    <?php if (count($reports) > 0) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Member Name</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>House Number</th>
                    <th>Materials</th>
                    <th>Report Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                        <td><?php echo htmlspecialchars($report['member_name']); ?></td>
                        <td><?php echo htmlspecialchars($report['name']); ?></td>
                        <td><?php echo htmlspecialchars($report['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['house_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['materials']); ?></td>
                        <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                        <td>
                            <a href="edit_field_report.php?id=<?php echo $report['report_id']; ?>">Edit</a> | 
                            <a href="view_field_report.php?id=<?php echo $report['report_id']; ?>">View</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No field reports found for your members.</p>
    <?php } ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
