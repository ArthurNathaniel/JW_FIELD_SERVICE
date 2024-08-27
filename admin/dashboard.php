<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit();
}
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
</head>
<body>
<?php include 'header.php'; ?>
<div class="scrolls">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>This is your admin dashboard.</p>
</div>    
    <?php include 'footer.php'; ?>
</body>
</html>
