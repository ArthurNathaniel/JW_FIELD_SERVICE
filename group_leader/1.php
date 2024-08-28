<?php
session_start();
require_once 'db.php';

// Check if the group leader is logged in
// if (!isset($_SESSION['leader_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Handle filtering by day, month/year, or year
$filter_condition = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filter_day'])) {
        $day = $_POST['day'];
        $filter_condition = "AND report_date = '$day'";
    } elseif (isset($_POST['filter_month_year'])) {
        $month = $_POST['month'];
        $year = $_POST['year'];
        $filter_condition = "AND MONTH(report_date) = $month AND YEAR(report_date) = $year";
    } elseif (isset($_POST['filter_year'])) {
        $year = $_POST['year'];
        $filter_condition = "AND YEAR(report_date) = $year";
    }
}

// Fetch materials summary and total members met
$sql = "SELECT materials, COUNT(*) as count 
        FROM field_reports 
        WHERE group_leader_id = ? $filter_condition
        GROUP BY materials";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['leader_id']);
$stmt->execute();
$result = $stmt->get_result();

$materials_summary = [];
$total_members = 0;
while ($row = $result->fetch_assoc()) {
    $materials = explode(', ', $row['materials']);
    foreach ($materials as $material) {
        if (!isset($materials_summary[$material])) {
            $materials_summary[$material] = 0;
        }
        $materials_summary[$material] += $row['count'];
    }
    $total_members += $row['count'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materials Summary</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <h2>Materials Summary</h2>
        <p>Total Number of People Met: <?php echo $total_members; ?></p>
    </div>

    <!-- Filter by Specific Day -->
    <div class="forms">
        <form method="POST" class="filter_form">
            <label for="day">Filter by Day:</label>
            <input type="date" name="day" id="day" required>
            <button type="submit" name="filter_day">Filter</button>
        </form>
    </div>

    <!-- Filter by Month and Year -->
    <form method="POST" class="filter_form">
        <div class="forms">
            <label for="month">Filter by Month and Year:</label>
            <select name="month" id="month" required>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select name="year" id="year" required>
                <?php for ($i = 2024; $i <= 4090; $i++) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="filter_month_year">Filter</button>
        </div>
    </form>

    <!-- Filter by Year Only -->
    <form method="POST" class="filter_form">
        <div class="forms">
            <label for="year_only">Filter by Year:</label>
            <select name="year" id="year_only" required>
                <?php for ($i = 2024; $i <= 4090; $i++) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="filter_year">Filter</button>
        </div>
    </form>

    <!-- Chart for Materials Summary -->
    <div class="forms">
        <canvas id="materialsChart"></canvas>
    </div>
</div>

<script>
// Prepare data for Chart.js
const labels = <?php echo json_encode(array_keys($materials_summary)); ?>;
const data = <?php echo json_encode(array_values($materials_summary)); ?>;

const ctx = document.getElementById('materialsChart').getContext('2d');
const materialsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: '# of Members',
            data: data,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
