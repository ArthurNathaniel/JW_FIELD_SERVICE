<?php
require_once 'db.php';
session_start();

// Check if the group leader is logged in
if (!isset($_SESSION['group_leader_id'])) {
    header("location: login.php");
    exit();
}

// Validate and get member ID
$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;

if ($member_id == 0) {
    die("Invalid member.");
}

// Fetch the member's name
$sql = "SELECT name FROM members WHERE id = ? AND group_leader_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $member_id, $_SESSION['group_leader_id']);
$stmt->execute();
$stmt->bind_result($member_name);
$stmt->fetch();
$stmt->close();

if (!$member_name) {
    die("Member not found.");
}

// Initialize variables
$reports = [];
$materials_summary = [];

// Handle day filter
if (isset($_POST['filter_day'])) {
    $filter_day = $_POST['day'];
    
    $sql = "SELECT name, phone_number, house_number, materials FROM field_reports WHERE member_id = ? AND report_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $member_id, $filter_day);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
        $materials = explode(', ', $row['materials']);
        foreach ($materials as $material) {
            if (!isset($materials_summary[$material])) {
                $materials_summary[$material] = 0;
            }
            $materials_summary[$material]++;
        }
    }

    $stmt->close();
}

// Handle month and year filter
if (isset($_POST['filter_month_year'])) {
    $filter_month = $_POST['month'];
    $filter_year = $_POST['year'];

    $sql = "SELECT name, phone_number, house_number, materials FROM field_reports WHERE member_id = ? AND MONTH(report_date) = ? AND YEAR(report_date) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $member_id, $filter_month, $filter_year);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
        $materials = explode(', ', $row['materials']);
        foreach ($materials as $material) {
            if (!isset($materials_summary[$material])) {
                $materials_summary[$material] = 0;
            }
            $materials_summary[$material]++;
        }
    }

    $stmt->close();
}

// Handle year filter
if (isset($_POST['filter_year'])) {
    $filter_year = $_POST['year'];

    $sql = "SELECT name, phone_number, house_number, materials FROM field_reports WHERE member_id = ? AND YEAR(report_date) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $member_id, $filter_year);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
        $materials = explode(', ', $row['materials']);
        foreach ($materials as $material) {
            if (!isset($materials_summary[$material])) {
                $materials_summary[$material] = 0;
            }
            $materials_summary[$material]++;
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports - <?php echo htmlspecialchars($member_name); ?></title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/view.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <h2>Reports for <?php echo htmlspecialchars($member_name); ?></h2>
    <!-- Filter by Specific Day -->
  
        <form method="POST" class="filter_form">
        <div class="forms">
            <label for="day">Filter by Day:</label>
            <input type="date" name="day" id="day" required>
            <br>
            <button type="submit" name="filter_day">Filter</button>
            </div>
        </form>
   

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
            <br>
            <select name="year" id="year" required>
                <?php for ($i = 2024; $i <= 4090; $i++) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
            <br>
         
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
            <br>
            <button type="submit" name="filter_year">Filter</button>
        </div>
    </form>

    <!-- Display Reports and Materials Summary -->
    <?php if (!empty($reports)) { ?>
        <h3>Reports Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>House Number</th>
                    <th>Materials</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['name']); ?></td>
                        <td><?php echo htmlspecialchars($report['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['house_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['materials']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3>Materials Summary</h3>
        <canvas id="materialsChart"></canvas>
        <script>
            const ctx = document.getElementById('materialsChart').getContext('2d');
            const materialsData = {
                labels: <?php echo json_encode(array_keys($materials_summary)); ?>,
                datasets: [{
                    label: 'Material Count',
                    data: <?php echo json_encode(array_values($materials_summary)); ?>,
                    backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(25, 00, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)',
                                'rgb(255, 19, 54)',
                            ],
                }]
            };
            const config = {
                // type: 'doughnut',
                type: 'bar',
                data: materialsData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            const materialsChart = new Chart(ctx, config);
        </script>
    <?php } else { ?>
        <p>No reports found for the selected period.</p>
    <?php } ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
