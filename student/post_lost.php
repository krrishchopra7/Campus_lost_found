<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = (int) $_SESSION['user_id'];
    $item_name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $date = $_POST['date'] ?? null;

    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

        if (in_array($_FILES['image']['type'], $allowed_types, true)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = "uploads/" . $file_name;
            }
        }
    }

    $sql = "INSERT INTO items
            (user_id, type, item_name, description, category, location, date, image)
            VALUES (?, 'lost', ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $item_name, $description, $category, $location, $date, $image);

    if ($stmt->execute()) {
        $message = "Lost item posted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Lost Item</title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
<header class="top-nav">
    <div class="top-nav-inner">
        <div class="brand">FoundBridge</div>
        <nav class="nav-links">
            <a href="../dashboard.php">Dashboard</a>
            <a href="view_lost.php">Lost Items</a>
            <a href="view_found.php">Found Items</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </div>
</header>

<main class="app-shell">
    <div class="page-head">
        <h2>Report Lost Item</h2>
        <a class="btn btn-secondary" href="../dashboard.php">Back</a>
    </div>

    <?php if ($message !== ''): ?>
        <p class="msg <?php echo str_starts_with($message, 'Error') ? 'msg-error' : 'msg-success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <section class="card">
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Item Name</label>
            <input id="name" type="text" name="name" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>

            <label for="category">Category</label>
            <input id="category" type="text" name="category">

            <label for="location">Location</label>
            <input id="location" type="text" name="location">

            <label for="date">Date Lost</label>
            <input id="date" type="date" name="date">

            <label for="image">Upload Image (optional)</label>
            <input id="image" type="file" name="image" accept="image/*">

            <div class="actions">
                <button type="submit" class="btn btn-primary">Submit Report</button>
            </div>
        </form>
    </section>
</main>
</body>
</html>
