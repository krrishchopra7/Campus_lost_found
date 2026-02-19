<?php
session_start();
require '../config/database.php';

$sql = "SELECT items.*, users.name AS posted_by 
        FROM items 
        JOIN users ON items.user_id = users.id
        WHERE type = 'found'
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Found Items</title>
</head>
<body>

<h2>Found Items</h2>

<?php while($row = $result->fetch_assoc()) { ?>

    <div style="border:1px solid black; padding:10px; margin:10px;">
        <h3><?php echo $row['name']; ?></h3>
        <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
        <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
        <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
        <p><strong>Posted By:</strong> <?php echo $row['posted_by']; ?></p>

        <?php if (!empty($row['image_path'])) { ?>
            <img src="../<?php echo $row['image_path']; ?>" width="150">
        <?php } ?>

    </div>

<?php } ?>

<br>
<a href="../dashboard.php">Back to Dashboard</a>

</body>
</html>
