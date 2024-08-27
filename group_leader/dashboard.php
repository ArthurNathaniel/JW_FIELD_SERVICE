<?php
session_start();
if (!isset($_SESSION["group_leader_id"])) {
    header("location: login_group_leader.php");
    exit();
}

// Fetch group leader details
require_once 'db.php';
$group_leader_id = $_SESSION['group_leader_id'];

// Fetch group leader information
$sql = "SELECT name, group_name FROM group_leaders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_leader_id);
$stmt->execute();
$result = $stmt->get_result();
$leader = $result->fetch_assoc();
$stmt->close();

// Fetch total members and gender distribution in the group
$sql = "SELECT COUNT(*) as total_members, 
               SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male_count,
               SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female_count
        FROM members WHERE group_leader_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_leader_id);
$stmt->execute();
$result = $stmt->get_result();
$group_stats = $result->fetch_assoc();
$stmt->close();

$conn->close();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
    <div class="dashboard">
        <h2>Welcome, <?php echo htmlspecialchars($leader['name']); ?>!</h2>
        <p>You are the leader of <strong><?php echo htmlspecialchars($leader['group_name']); ?></strong>.</p>
        <p>Total Number of People in Your Group: <strong><?php echo $group_stats['total_members']; ?></strong></p>
        
        <!-- Gender Distribution Chart -->
      <div class="chart">
      <canvas id="genderChart" width="400" height="200"></canvas>
      </div>
    </div>
    <?php include 'footer.php'; ?>
    
    <script>
        // Prepare gender data
        const genderData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [<?php echo $group_stats['male_count']; ?>, <?php echo $group_stats['female_count']; ?>],
                backgroundColor: ['#36A2EB', '#FF6384'],
                borderColor: ['#36A2EB', '#FF6384'],
                borderWidth: 1
            }]
        };

        // Config for the chart
        const config = {
            type: 'pie',
            data: genderData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            },
        };

        // Render the chart
        var ctx = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(ctx, config);
    </script>
</body>
</html>
