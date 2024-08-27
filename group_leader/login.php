<?php
session_start();
require_once 'db.php';

if (isset($_SESSION["group_leader_id"])) {
    header("location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM group_leaders WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['group_leader_id'] = $row['id'];
            header("location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Leader Login</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="forms_all">
    <div class="forms">
        <div class="logo"></div>
        <h2>Group Leader Login</h2>
    </div>
    
           
             
            <div class="error_message">
    <?php if ($error) { echo "<p class='error'>$error</p>"; } ?>
    <span class="close_btn"><i class="fas fa-times"></i></span> 
    </div>
    <form action="" method="POST">
        <div class="forms">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="forms">
            <label for="password">Password</label>
            <input type="password"  name="password" id="pin" required>
        </div>
        <div class="form">
                <input type="checkbox" id="showPin"> show password
            </div>
        <div class="forms">
            <button type="submit">Login</button>
        </div>
        <div class="forms">
            <p><a href="forgot_password.php">Forgot password</a></p>
        </div>
    </form>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.close_btn');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
        });

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
