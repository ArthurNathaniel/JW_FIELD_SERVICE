<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login_member.php");
    exit();
}

// Fetch member details from the database
$member_id = $_SESSION['member_id'];
$sql = "SELECT name, email, gender, phone_number FROM members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
<?php include 'header.php'; ?>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($member['name']); ?>!</h2>
        <p>Here is your account information:</p>
        <table class="member-info">
            <tr>
                <th>Name:</th>
                <td><?php echo htmlspecialchars($member['name']); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
            </tr>
            <tr>
                <th>Gender:</th>
                <td><?php echo htmlspecialchars($member['gender']); ?></td>
            </tr>
            <tr>
                <th>Phone Number:</th>
                <td><?php echo htmlspecialchars($member['phone_number']); ?></td>
            </tr>
            
        </table>
        <div class="actions">
            <a href="edit_profile.php" class="btn">Edit Profile</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
<?php include 'footer.php'; ?>
</body>
</html>
