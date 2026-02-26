<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

if ($role === 'admin') {
    $usersCount = (int)($conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0);
    $itemsOpen = (int)($conn->query("SELECT COUNT(*) AS total FROM items WHERE status = 'open'")->fetch_assoc()['total'] ?? 0);
    $itemsReturned = (int)($conn->query("SELECT COUNT(*) AS total FROM items WHERE status = 'returned'")->fetch_assoc()['total'] ?? 0);
    $claimsPending = (int)($conn->query("SELECT COUNT(*) AS total FROM claims WHERE status = 'pending'")->fetch_assoc()['total'] ?? 0);

    $recentQuery = "SELECT items.item_name, items.type, items.status, users.name AS owner_name
                    FROM items
                    JOIN users ON items.user_id = users.id
                    ORDER BY items.created_at DESC
                    LIMIT 5";
    $recentItems = $conn->query($recentQuery);
} else {
    $myPosts = (int)($conn->query("SELECT COUNT(*) AS total FROM items WHERE user_id = {$user_id}")->fetch_assoc()['total'] ?? 0);
    $openItems = (int)($conn->query("SELECT COUNT(*) AS total FROM items WHERE status = 'open'")->fetch_assoc()['total'] ?? 0);
    $myPendingClaims = (int)($conn->query("SELECT COUNT(*) AS total FROM claims WHERE claimant_id = {$user_id} AND status = 'pending'")->fetch_assoc()['total'] ?? 0);
    $myApprovedClaims = (int)($conn->query("SELECT COUNT(*) AS total FROM claims WHERE claimant_id = {$user_id} AND status = 'approved'")->fetch_assoc()['total'] ?? 0);

    $recentQuery = "SELECT item_name, type, status
                    FROM items
                    WHERE user_id = ?
                    ORDER BY created_at DESC
                    LIMIT 5";
    $recentStmt = $conn->prepare($recentQuery);
    $recentStmt->bind_param("i", $user_id);
    $recentStmt->execute();
    $recentItems = $recentStmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<header class="top-nav">
    <div class="top-nav-inner">
        <div class="brand">FoundBridge</div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</header>

<main class="app-shell">
    <div class="page-head">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
        <span class="pill pill-<?php echo htmlspecialchars($role); ?>"><?php echo htmlspecialchars($role); ?></span>
    </div>
    <p class="muted">Track activity, take action fast, and keep the campus recovery system moving.</p>

    <section class="card hero-panel">
        <h3><?php echo $role === 'admin' ? 'System Control Center' : 'Student Quick Overview'; ?></h3>
        <p class="muted">
            <?php echo $role === 'admin'
                ? 'Monitor all users, claims, and item lifecycle from one place.'
                : 'Post updates, follow your claim statuses, and manage your item reports.'; ?>
        </p>
    </section>

    <?php if ($role === 'admin') { ?>
        <section class="card-grid">
            <article class="stat-card">
                <p class="value"><?php echo $usersCount; ?></p>
                <p class="label">Registered Users</p>
            </article>
            <article class="stat-card">
                <p class="value"><?php echo $itemsOpen; ?></p>
                <p class="label">Open Items</p>
            </article>
            <article class="stat-card">
                <p class="value"><?php echo $claimsPending; ?></p>
                <p class="label">Pending Claims</p>
            </article>
            <article class="stat-card">
                <p class="value"><?php echo $itemsReturned; ?></p>
                <p class="label">Returned Items</p>
            </article>
        </section>
    <?php } else { ?>
        <section class="card-grid">
            <article class="stat-card">
                <p class="value"><?php echo $myPosts; ?></p>
                <p class="label">My Posts</p>
            </article>
            <article class="stat-card">
                <p class="value"><?php echo $openItems; ?></p>
                <p class="label">Open Campus Items</p>
            </article>
            <article class="stat-card">
                <p class="value"><?php echo $myPendingClaims; ?></p>
                <p class="label">My Pending Claims</p>
            </article>
            <article class="stat-card">
                <p class="value"><?php echo $myApprovedClaims; ?></p>
                <p class="label">My Approved Claims</p>
            </article>
        </section>
    <?php } ?>

    <div class="split-grid">
    <?php if ($role === 'admin') { ?>
        <section class="card">
            <h3>Admin Module</h3>
            <div class="actions">
                <a class="btn btn-primary" href="admin/dashboard.php">Admin Dashboard</a>
                <a class="btn btn-secondary" href="admin/view_users.php">View Users</a>
                <a class="btn btn-secondary" href="admin/view_items.php">View Items</a>
                <a class="btn btn-secondary" href="admin/view_claims.php">View Claims</a>
            </div>
        </section>
    <?php } else { ?>
        <section class="card">
            <h3>Student Module</h3>
            <div class="actions">
                <a class="btn btn-primary" href="student/post_lost.php">Post Lost Item</a>
                <a class="btn btn-primary" href="student/post_found.php">Post Found Item</a>
                <a class="btn btn-secondary" href="student/view_lost.php">View Lost Items</a>
                <a class="btn btn-secondary" href="student/view_found.php">View Found Items</a>
                <a class="btn btn-secondary" href="student/my_claims.php">My Claims</a>
            </div>
        </section>
    <?php } ?>

        <section class="card">
            <h3><?php echo $role === 'admin' ? 'Recent Item Activity' : 'My Recent Posts'; ?></h3>
            <?php if ($recentItems && $recentItems->num_rows > 0) { ?>
                <div class="activity-list">
                    <?php while ($row = $recentItems->fetch_assoc()) { ?>
                        <div class="activity-row">
                            <div>
                                <p class="activity-title"><?php echo htmlspecialchars($row['item_name']); ?></p>
                                <p class="activity-sub">
                                    <?php echo htmlspecialchars(ucfirst($row['type'])); ?>
                                    <?php if ($role === 'admin') { ?>
                                        • <?php echo htmlspecialchars($row['owner_name']); ?>
                                    <?php } ?>
                                </p>
                            </div>
                            <span class="pill pill-<?php echo htmlspecialchars($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="muted">No activity yet.</p>
            <?php } ?>
        </section>
    </div>
</main>
</body>
</html>
