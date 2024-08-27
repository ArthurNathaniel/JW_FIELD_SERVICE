<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit();
}
$name = $phone_number = $group_name = $email = $password = "";
$name_err = $phone_number_err = $group_name_err = $email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim all inputs at once
    $name = trim($_POST["name"]);
    $phone_number = trim($_POST["phone_number"]);
    $group_name = trim($_POST["group_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate name
    if (empty($name)) {
        $name_err = "Please enter a name.";
    }

    // Validate phone number
    if (empty($phone_number)) {
        $phone_number_err = "Please enter a phone number.";
    } else {
        // Check if phone number already exists
        $sql = "SELECT id FROM group_leaders WHERE phone_number = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $phone_number);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $phone_number_err = "This phone number is already registered.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate group
    if (empty($group_name)) {
        $group_name_err = "Please select a group.";
    } else {
        $sql = "SELECT id FROM group_leaders WHERE group_name = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $group_name);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $group_name_err = "This group is already assigned.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate email
    if (empty($email)) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email.";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM group_leaders WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "This email is already registered.";
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

    // Check input errors before inserting into database
    if (empty($name_err) && empty($phone_number_err) && empty($group_name_err) && empty($email_err) && empty($password_err)) {
        $sql = "INSERT INTO group_leaders (name, phone_number, group_name, email, password) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $name, $phone_number, $group_name, $email, $hashed_password);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            if ($stmt->execute()) {
                echo "<script>alert('Group Leader registered successfully!'); window.location.href = 'view_group_leader.php';</script>";
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
    <title>Register Group Leader</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<?php include 'header.php'; ?>
    <div class="scrolls">
    <div class="forms_alls">
        <div class="forms">
            <div class="logo"></div>
            <h2>Register Group Leader</h2>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="forms">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                <span class="error"><?php echo $name_err; ?></span>
            </div>
            <div class="forms">
                <label>Phone Number:</label>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                <span class="error"><?php echo $phone_number_err; ?></span>
            </div>
            <div class="forms">
                <label>Group:</label>
                <select name="group_name" required>
                    <option value="" selected hidden>Select Group</option>
                    <?php
                    $groups = ['group one', 'group two', 'group three', 'group four', 'group five'];
                    foreach ($groups as $group) {
                        $sql = "SELECT id FROM group_leaders WHERE group_name = ?";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("s", $group);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                                if ($stmt->num_rows == 0) {
                                    echo "<option value=\"$group\" " . ($group_name == $group ? 'selected' : '') . ">$group</option>";
                                }
                            }
                            $stmt->close();
                        }
                    }
                    ?>
                </select>
                <span class="error"><?php echo $group_name_err; ?></span>
            </div>
            <div class="forms">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="forms">
                <label>Password:</label>
                <input type="password" name="password" id="pin" required minlength="6">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form">
                <input type="checkbox" id="showPin"> Show password
            </div>
            <div class="forms">
                <button type="submit">Register</button>
            </div>
        </form>
       
   
    </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        document.getElementById('showPin').addEventListener('change', function() {
            const pinInput = document.getElementById('pin');
            pinInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>
