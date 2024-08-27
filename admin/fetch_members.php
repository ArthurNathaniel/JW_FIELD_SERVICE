<?php
require_once 'db.php';

if (isset($_GET['group_leader_id'])) {
    $group_leader_id = $_GET['group_leader_id'];

    $sql = "SELECT name, gender, phone_number, email FROM members WHERE group_leader_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_leader_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='member_card'>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
            echo "<p><strong>Gender:</strong> " . htmlspecialchars($row['gender']) . "</p>";
            echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['phone_number']) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
            echo "</div>";
            echo "<hr>";
        }
    } else {
        echo "<p>No members found for this group leader.</p>";
    }

    $stmt->close();
}

$conn->close();
?>
