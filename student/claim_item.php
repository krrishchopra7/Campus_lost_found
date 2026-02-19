<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $item_id = $_POST['item_id'];
    $claimant_id = $_SESSION['user_id'];

    $sql = "INSERT INTO claims (item_id, claimant_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $item_id, $claimant_id);

    if ($stmt->execute()) {
        echo "Claim request sent successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<br>
<a href="view_found.php">Back</a>
