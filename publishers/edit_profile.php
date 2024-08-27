<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login_member.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$success = "";
$error = "";

// Fetch member details
$sql = "SELECT name, email, phone_number, gender FROM members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];

    $sql = "UPDATE members SET name = ?, email = ?, phone_number = ?, gender = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone_number, $gender, $member_id);

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <div class="logo"></div>
        <h2>Edit Profile</h2>
    </div>

    <?php if ($success) { echo "<p class='success'>$success</p>"; } ?>
    <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>

    <form action="edit_profile.php" method="POST">
        <div class="forms">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
        </div>
        <div class="forms">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
        </div>
        <div class="forms">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($member['phone_number']); ?>" required>
        </div>
        <div class="forms">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php if ($member['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($member['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
        <div class="forms">
            <button type="submit">Update Profile</button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
