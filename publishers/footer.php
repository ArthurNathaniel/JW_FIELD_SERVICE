<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="footer_all">
    <div class="ft_icons">
    <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <span><i class="fa-solid fa-house"></i></span>
            Home
        </a>
    </div>
    <div class="ft_icons">
    <a href="add_field_report.php" class="<?= ($current_page == 'add_field_report.php') ? 'active' : ''; ?>">

        <span><i class="fa-solid fa-user-plus"></i></span>
           Add Field Report
        </a>
    </div>
    <div class="ft_icons">
    <a href="view_field_reports.php" class="<?= ($current_page == 'view_field_reports.php') ? 'active' : ''; ?>">

        <span><i class="fa-solid fa-users"></i></span>
        View Field Report
        </a>
    </div>
    <div class="ft_icons">
    <a href="logout.php" class="<?= ($current_page == 'logout.php') ? 'active' : ''; ?>">

        <span><i class="fa-solid fa-right-from-bracket"></i></span>
            Logout
        </a>
    </div>
</div>
