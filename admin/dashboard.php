<?php
session_start();
require_once 'db.php';  // Ensure the database connection is included

if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit();
}

// Fetch group leaders
$sql = "SELECT id AS group_leader_id, name AS leader_name, group_name FROM group_leaders";
$leadersResult = $conn->query($sql);

// Fetch total groups
$sql = "SELECT COUNT(*) AS total_groups FROM group_leaders";
$totalGroupsResult = $conn->query($sql);
$totalGroups = $totalGroupsResult->fetch_assoc()['total_groups'];

// Fetch total members
$sql = "SELECT COUNT(*) AS total_members FROM members";
$totalMembersResult = $conn->query($sql);
$totalMembers = $totalMembersResult->fetch_assoc()['total_members'];

// Fetch members per group
$sql = "SELECT gl.group_name, COUNT(m.id) AS member_count
        FROM group_leaders gl
        LEFT JOIN members m ON gl.id = m.group_leader_id
        GROUP BY gl.group_name";
$membersPerGroupResult = $conn->query($sql);

// Fetch gender distribution in each group
$sql = "SELECT gl.group_name, m.gender, COUNT(m.id) AS gender_count
        FROM group_leaders gl
        LEFT JOIN members m ON gl.id = m.group_leader_id
        GROUP BY gl.group_name, m.gender";
$genderDistributionResult = $conn->query($sql);

// Fetch total gender distribution
$sql = "SELECT gender, COUNT(*) AS gender_count FROM members GROUP BY gender";
$totalGenderResult = $conn->query($sql);

// Prepare data for JavaScript
$membersPerGroupData = [];
while ($row = $membersPerGroupResult->fetch_assoc()) {
    $membersPerGroupData[] = $row;
}

$genderDistributionData = [];
while ($row = $genderDistributionResult->fetch_assoc()) {
    $genderDistributionData[$row['group_name']][$row['gender']] = $row['gender_count'];
}

$totalGenderData = [];
while ($row = $totalGenderResult->fetch_assoc()) {
    $totalGenderData[$row['gender']] = $row['gender_count'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="scrolls dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>This is your admin dashboard.</p>
    <h2>Statistics</h2>
    <div class="charts">
    
    <div class="chart">
        <canvas id="totalGroupsChart"></canvas>
    </div>
    <div class="chart">
        <canvas id="totalMembersChart"></canvas>
    </div>
    <div class="chart">
        <canvas id="membersPerGroupChart"></canvas>
    </div>
    <div class="chart">
        <canvas id="genderDistributionChart"></canvas>
    </div>
    <div class="chart">
        <canvas id="totalGenderChart"></canvas>
    </div>
</div>
<div class="group_table">
    <h2>Group Leaders</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Group</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($leadersResult->num_rows > 0) {
                while($leader = $leadersResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($leader['leader_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($leader['group_name']) . "</td>";
                    echo "<td><button class='view-members' data-leader-id='" . htmlspecialchars($leader['group_leader_id']) . "'>View Members</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No group leaders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Modal -->
<div id="memberModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Members</h2>
        <div id="membersList">
            <!-- Members will be loaded here via AJAX -->
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("memberModal");
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on a button, open the modal
    document.querySelectorAll('.view-members').forEach(function(button) {
        button.addEventListener('click', function() {
            var leaderId = this.getAttribute('data-leader-id');
            var membersList = document.getElementById('membersList');

            // Fetch members for this group leader
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_members.php?group_leader_id=' + leaderId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    membersList.innerHTML = xhr.responseText;
                    modal.style.display = "block";
                }
            };
            xhr.send();
        });
    });

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Data from PHP
    var totalGroups = <?php echo $totalGroups; ?>;
    var totalMembers = <?php echo $totalMembers; ?>;
    var membersPerGroup = <?php echo json_encode($membersPerGroupData); ?>;
    var genderDistribution = <?php echo json_encode($genderDistributionData); ?>;
    var totalGender = <?php echo json_encode($totalGenderData); ?>;

    // Total Groups Chart
    new Chart(document.getElementById('totalGroupsChart'), {
        type: 'pie',
        data: {
            labels: ['Total Groups'],
            datasets: [{
                data: [totalGroups],
                backgroundColor: ['#36a2eb']
            }]
        },
        options: {
            responsive: true,
            scales: {
             
                y: {
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Total Members Chart
    new Chart(document.getElementById('totalMembersChart'), {
        type: 'pie',
        data: {
            labels: ['Total Members'],
            datasets: [{
                data: [totalMembers],
                backgroundColor: ['#ff6384']
            }]
        },
        // options: {
        //     responsive: true,
        //     plugins: {
        //         legend: {
        //             display: false
        //         }
        //     }
        // }

        options: {
            responsive: true,
            scales: {
               
                y: {
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    beginAtZero: true
                }
            }
        }
        
    });

    // Members Per Group Chart
    var groupNames = membersPerGroup.map(item => item.group_name);
    var memberCounts = membersPerGroup.map(item => item.member_count);

    new Chart(document.getElementById('membersPerGroupChart'), {
        type: 'bar',
        data: {
            labels: groupNames,
            datasets: [{
                label: 'Number of Members',
                data: memberCounts,
                backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)'
                            ],
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Group Name'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Members'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Gender Distribution in Each Group Chart
    var groupNames = Object.keys(genderDistribution);
    var genderData = {};
    var genderLabels = ['Male', 'Female'];

    genderLabels.forEach(gender => {
        genderData[gender] = groupNames.map(group => genderDistribution[group][gender] || 0);
    });

    new Chart(document.getElementById('genderDistributionChart'), {
        type: 'bar',
        data: {
            labels: groupNames,
            datasets: [
                {
                    label: 'Male',
                    data: genderData['Male'],
                    backgroundColor: '#36a2eb'
                },
                {
                    label: 'Female',
                    data: genderData['Female'],
                    backgroundColor: '#ff6384'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Group Name'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Total Gender Distribution Chart
    var genders = Object.keys(totalGender);
    var genderCounts = Object.values(totalGender);

    new Chart(document.getElementById('totalGenderChart'), {
        type: 'pie',
        data: {
            labels: genders,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#36a2eb', '#ff6384']
            }]
        },
        // options: {
        //     responsive: true,
        //     plugins: {
        //         legend: {
        //             position: 'bottom'
        //         }
        //     }
        // }

        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Total Gender'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    beginAtZero: true
                }
            }
        }
    });
});

</script>
</body>
</html>

<!-- <?php
$conn->close();
?> -->
