<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idToDelete = $_POST['id'];
    $deleteSql = "DELETE FROM group_leaders WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $idToDelete);
    if ($stmt->execute()) {
        echo "<script>alert('Group leader deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete group leader');</script>";
    }
    $stmt->close();
}

// Fetch group leaders data
$sql = "SELECT id, name, phone_number, group_name, email FROM group_leaders";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Group Leaders</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="scrolls">
    <div class="table_container">
        <div class="logo"></div>
        <h2>Group Leaders</h2>
        <div class="search forms">
            <input type="text" id="searchInput" placeholder="Search by name, phone, group, or email">
        </div>
        <div class="card_grid" id="cardContainer">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<h3>" . htmlspecialchars($row['group_name']) . "</h3>";
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['phone_number']) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
                    echo "<div class='card_delete'>";
                    echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this group leader?\");'>";
                    echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<button type='submit' class='delete-btn'><i class='fa-solid fa-trash-can'></i></button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No group leaders found.</p>";
            }
            ?>
        </div>
    </div>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input, filter, cards, cardContainer, h3, p, i, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            cardContainer = document.getElementById('cardContainer');
            cards = cardContainer.getElementsByClassName('card');

            for (i = 0; i < cards.length; i++) {
                h3 = cards[i].getElementsByTagName("h3")[0];
                p = cards[i].getElementsByTagName("p");
                if (h3 || p) {
                    txtValue = h3.textContent || h3.innerText;
                    for (var j = 0; j < p.length; j++) {
                        txtValue += p[j].textContent || p[j].innerText;
                    }
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        cards[i].style.display = "";
                    } else {
                        cards[i].style.display = "none";
                    }
                }       
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
