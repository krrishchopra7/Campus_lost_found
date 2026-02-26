<?php
if (!isset($base_path)) {
    $base_path = '';
}
?>
<nav>
    <a href="<?php echo htmlspecialchars($base_path); ?>index.php">Home</a> |
    <a href="<?php echo htmlspecialchars($base_path); ?>dashboard.php">Dashboard</a> |
    <a href="<?php echo htmlspecialchars($base_path); ?>student/view_lost.php">Lost Items</a> |
    <a href="<?php echo htmlspecialchars($base_path); ?>student/view_found.php">Found Items</a> |
    <?php if (isset($_SESSION['user_id'])) { ?>
        <a href="<?php echo htmlspecialchars($base_path); ?>logout.php">Logout</a>
    <?php } else { ?>
        <a href="<?php echo htmlspecialchars($base_path); ?>login.php">Login</a> |
        <a href="<?php echo htmlspecialchars($base_path); ?>register.php">Register</a>
    <?php } ?>
</nav>
<hr>
