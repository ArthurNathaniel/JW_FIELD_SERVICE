<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION["group_leader_id"])) {
    header("location: login.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert member data into the database
    $sql = "INSERT INTO members (name, gender, phone_number, email, password, group_leader_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $gender, $phone_number, $email, $password, $_SESSION['group_leader_id']);

    if ($stmt->execute()) {
        $success = "Member registered successfully!";
    } else {
        $error = "Failed to register member. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Member</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="forms_alls">
    <div class="forms">
        <div class="logo"></div>
        <h2>Register Member</h2>
    </div>

    <?php if ($success) { echo "<p class='success'>$success</p>"; } ?>
    <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>

    <form action="" method="POST">
        <div class="forms">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="forms">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="forms">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" required>
        </div>
        <div class="forms">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="forms">
            <label for="password">Password</label>
            <input type="password" id="pin" name="password" required>
        </div>
        <div class="form">
                <input type="checkbox" id="showPin"> show password
            </div>
        <div class="forms">
            <button type="submit">Register Member</button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
<script>
     document.getElementById('showPin').addEventListener('change', function() {
            var pinInput = document.getElementById('pin');
            if (this.checked) {
                pinInput.type = 'text';
            } else {
                pinInput.type = 'password';
            }
        });
</script>
</body>
</html>

<?php
$conn->close();
?>
