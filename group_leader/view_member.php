<?php
require_once 'db.php';
session_start();

// Check if the group leader is logged in
if (!isset($_SESSION['group_leader_id'])) {
    header("location: login.php");
    exit();
}

// Fetch group leader's members
$group_leader_id = $_SESSION['group_leader_id'];
$sql = "SELECT name, gender, phone_number, email FROM members WHERE group_leader_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_leader_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Members</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="table_container">
    <h2>My Members</h2>
    <div class="card_grid">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p><strong>Gender:</strong> " . htmlspecialchars($row['gender']) . "</p>";
                echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['phone_number']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>You have not registered any members yet.</p>";
        }
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
