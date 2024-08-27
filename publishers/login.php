<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query to check email and password
    $sql = "SELECT id, password FROM members WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $member['password'])) {
            $_SESSION['member_id'] = $member['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
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
    <title>Member Login</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="forms_all">
    <div class="forms">
        <div class="logo"></div>
        <h2>Pulisher Login</h2>
    </div>
    
        <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
        <form action="" method="POST">
            <div class="forms">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="forms">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form">
                <input type="checkbox" id="showPassword"> Show Password
            </div>
            <div class="forms">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="forms">
        <p>Forget password <a href="forgot_password_member.php">Click here</a>.</p>
        </div>
    </div>
</div>
<script>
    document.getElementById('showPassword').addEventListener('change', function() {
        var passwordInput = document.getElementById('password');
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>
</body>
</html>
