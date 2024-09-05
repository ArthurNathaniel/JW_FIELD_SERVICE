<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- SEO Elements -->
    <title>JW Field Service | Efficient Recording & Analytics Portal</title>
    <meta name="description" content="JW Field Service is a web application designed to help Jehovah's Witnesses efficiently record their field service activities and gain valuable insights through detailed analytics.">
    <?php include './cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="./css/view.css">
</head>

<body>
    <?php include './admin/header.php'; ?>
    <div class="title">
        <h1>JW Field Service Access Portal</h1>
    </div>
    <div class="login_grid">
        <a href="./admin/login.php">
            <div class="login">
                Admin
            </div>
        </a>
        <a href="./group_leader/login.php">
            <div class="login">
                Group Leader
            </div>
        </a>
        <a href="./publishers/login.php">
            <div class="login">
                Publisher
            </div>
        </a>
    </div>
</body>

</html>