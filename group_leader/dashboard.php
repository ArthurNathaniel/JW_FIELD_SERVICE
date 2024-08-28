<?php
session_start();
if (!isset($_SESSION["group_leader_id"])) {
    header("location: login.php");
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

// Fetch material summary for all members in the group
$sql = "SELECT 
            SUM(CASE WHEN materials LIKE '%Magazine%' THEN 1 ELSE 0 END) as magazine_count,
            SUM(CASE WHEN materials LIKE '%Audio%' THEN 1 ELSE 0 END) as audio_count,
            SUM(CASE WHEN materials LIKE '%Video%' THEN 1 ELSE 0 END) as video_count,
            COUNT(DISTINCT member_id) as total_reporters
        FROM field_reports 
        WHERE member_id IN (SELECT id FROM members WHERE group_leader_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_leader_id);
$stmt->execute();
$result = $stmt->get_result();
$material_stats = $result->fetch_assoc();
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
        <p>Total Number of People Reporting Materials: <strong><?php echo $material_stats['total_reporters']; ?></strong></p>
        
        <div class="charts">
            <!-- Gender Distribution Chart -->
            <div class="chart">
                <canvas id="genderChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Material Summary Chart -->
            <div class="chart">
                <canvas id="materialChart" width="400" height="200"></canvas>
            </div>
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

        // Config for the gender chart
        const genderConfig = {
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

        // Render the gender chart
        var ctxGender = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(ctxGender, genderConfig);

        // Prepare material data
        const materialData = {
            labels: ['Magazine', 'Audio', 'Video'],
            datasets: [{
                label: 'Materials Summary',
                data: [
                    <?php echo $material_stats['magazine_count']; ?>, 
                    <?php echo $material_stats['audio_count']; ?>, 
                    <?php echo $material_stats['video_count']; ?>
                ],
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56'],
                borderColor: ['#36A2EB', '#FF6384', '#FFCE56'],
                borderWidth: 1
            }]
        };

        // Config for the material chart
        const materialConfig = {
            type: 'bar',
            data: materialData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };

        // Render the material chart
        var ctxMaterial = document.getElementById('materialChart').getContext('2d');
        var materialChart = new Chart(ctxMaterial, materialConfig);
    </script>
</body>
</html>
