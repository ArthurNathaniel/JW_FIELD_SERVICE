<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}


// Fetch member details from the database
$member_id = $_SESSION['member_id'];
$sql = "SELECT name, email, gender, phone_number FROM members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <?php include '../cdn.php'; ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($member['name']); ?>!</h2>
        <!-- Swiper -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="../images/slide_1.jpg" alt=""></div>
                <div class="swiper-slide"><img src="../images/slide_2.jpg" alt=""></div>
            </div>
            
        </div>
        <div class="member-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($member['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($member['email']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($member['gender']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($member['phone_number']); ?></p>
        </div>
        <div class="actions">
            <a href="edit_profile.php" class="btns">Edit Profile</a>

        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 30,
            centeredSlides: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    </script>
</body>

</html>