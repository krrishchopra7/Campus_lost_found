<?php
session_start();
require '../config/database.php';

$sql = "SELECT items.*, users.name AS posted_by
        FROM items
        JOIN users ON items.user_id = users.id
        WHERE type = 'lost'
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Lost Items</title>
</head>
<body>

<h2>Lost Items</h2>

<?php while($row = $result->fetch_assoc()) { ?>

    <div style="border:1px solid black; padding:10px; margin:10px;">
        
        <!-- ✅ FIXED: item_name instead of name -->
        <h3><?php echo $row['item_name']; ?></h3>

        <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
        <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
        <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>

        <!-- ✅ Using alias posted_by -->
        <p><strong>Posted By:</strong> <?php echo $row['posted_by']; ?></p>

        <!-- ✅ FIXED: image instead of image_path -->
        <?php if (!empty($row['image'])) { ?>
            <img src="../<?php echo $row['image']; ?>" width="150">
        <?php } ?>

    </div>

<?php } ?>

<br>
<a href="../dashboard.php">Back to Dashboard</a>

</body>
</html>
