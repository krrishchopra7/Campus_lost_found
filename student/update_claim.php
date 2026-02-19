<?php
require '../config/database.php';

$id = $_GET['id'];
$action = $_GET['action'];

if ($action == "approved" || $action == "rejected") {

    // Update claim status
    $sql = "UPDATE claims SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();

    // If approved → also update item status
    if ($action == "approved") {

        // Get item_id from claim
        $getItem = $conn->prepare("SELECT item_id FROM claims WHERE id = ?");
        $getItem->bind_param("i", $id);
        $getItem->execute();
        $result = $getItem->get_result();
        $row = $result->fetch_assoc();

        $item_id = $row['item_id'];

        // Update item status
        $updateItem = $conn->prepare("UPDATE items SET status = 'claimed' WHERE id = ?");
        $updateItem->bind_param("i", $item_id);
        $updateItem->execute();
    }
}

header("Location: my_claims.php");
exit();
?>
