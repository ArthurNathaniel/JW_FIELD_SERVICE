<?php
require_once 'db.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM admins WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            header("location: dashboard.php");
                            exit();
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Admin Login</title>
    <?php include '../cdn.php' ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="forms_all">
        <div class="forms">
            <div class="logo"></div>
            <h2>Admin Login</h2>
        </div>

        <?php if (!empty($login_err)): ?>
            <div class="error_message">
                <span><?php echo $login_err; ?></span>
                <span class="close_btn"><i class="fas fa-times"></i></span> <!-- Font Awesome close icon -->
            </div>
        <?php endif; ?>


        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="forms">
                <label>Username: </label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>
            <div class="forms">
                <label>Password: </label>
                <input type="password" name="password" id="pin">
                <span><?php echo $password_err; ?></span>
            </div>
            <div class="form">
                <input type="checkbox" id="showPin"> show password
            </div>
            <div class="forms">
                <button type="submit">Login</button>
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