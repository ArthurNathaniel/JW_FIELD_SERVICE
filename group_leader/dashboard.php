<?php
session_start();
if (!isset($_SESSION["group_leader_id"])) {
    header("location: login_group_leader.php");
    exit();
}

// Fetch group leader details
require_once 'db.php';
$group_leader_id = $_SESSION['group_leader_id'];

$sql = "SELECT name, group_name FROM group_leaders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_leader_id);
$stmt->execute();
$result = $stmt->get_result();
$leader = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Leader Dashboard</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
<?php include 'header.php'; ?>
    <div class="dashboard_container">
        <h2>Welcome, <?php echo htmlspecialchars($leader['name']); ?>!</h2>
        <p>You are the leader of <strong><?php echo htmlspecialchars($leader['group_name']); ?></strong>.</p>
        
        <div class="dashboard_links">
            <a href="view_group_members.php" class="dashboard_link">
                <i class="fa-solid fa-users"></i> View Group Members
            </a>
            <a href="add_member.php" class="dashboard_link">
                <i class="fa-solid fa-user-plus"></i> Add New Member
            </a>
            <a href="edit_profile.php" class="dashboard_link">
                <i class="fa-solid fa-user-edit"></i> Edit Profile
            </a>
            <a href="logout.php" class="dashboard_link">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
