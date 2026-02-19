<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $date = $_POST['date'];

    $image_path = NULL;

    // Handle image upload (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

        if (in_array($_FILES['image']['type'], $allowed_types)) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $image_path = "uploads/" . $file_name;
        }
    }

    $sql = "INSERT INTO items
        (user_id, type, item_name, description, category, location, date, image)
        VALUES (?, 'lost', ?, ?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $name, $description, $category, $location, $date, $image_path);

    if ($stmt->execute()) {
        echo "Lost item posted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Lost Item</title>
</head>
<body>

<h2>Post Lost Item</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Item Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Category:</label><br>
    <input type="text" name="category"><br><br>

    <label>Location:</label><br>
    <input type="text" name="location"><br><br>

    <label>Date Lost:</label><br>
    <input type="date" name="date"><br><br>

    <label>Upload Image (optional):</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">Submit</button>
</form>

<br>
<a href="../dashboard.php">Back to Dashboard</a>

</body>
</html>
