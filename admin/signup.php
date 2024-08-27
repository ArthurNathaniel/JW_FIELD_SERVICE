<?php
require_once 'db.php';

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim all inputs at once
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Validate username
    if (empty($username)) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM admins WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate password
    if (empty($password)) {
        $password_err = "Please enter a password.";
    } elseif (strlen($password) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }

    // Validate confirm password
    if (empty($confirm_password)) {
        $confirm_password_err = "Please confirm the password.";
    } elseif ($password != $confirm_password) {
        $confirm_password_err = "Passwords do not match.";
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $hashed_password);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            if ($stmt->execute()) {
                header("location: login.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <?php include '../cdn.php' ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="forms_all">
        <div class="forms">
            <div class="logo"></div>
            <h2>Admin Signup</h2>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="forms">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <span class="error"><?php echo $username_err; ?></span>
            </div>    
            <div class="forms">
                <label>Password:</label>
                <input type="password" name="password" id="pin" required minlength="6">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="forms">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" id="pin" required minlength="6">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form">
                <input type="checkbox" id="showPin"> Show password
            </div>
            <div class="forms">
                <button type="submit">Signup</button>
            </div>
        </form>
        <div class="forms">
            <a href="login.php">Already have an account? Login here</a>
        </div>
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
            const pinInputs = document.querySelectorAll('#pin');
            pinInputs.forEach(pinInput => {
                pinInput.type = this.checked ? 'text' : 'password';
            });
        });
    </script>
</body>
</html>
