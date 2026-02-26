<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: view_found.php");
    exit();
}

$item_id = (int)($_POST['item_id'] ?? 0);
$claimant_id = (int)$_SESSION['user_id'];
$message = trim($_POST['message'] ?? '');
$return_to = $_POST['return_to'] ?? 'view_found.php';
if (!in_array($return_to, ['view_found.php', 'view_lost.php'], true)) {
    $return_to = 'view_found.php';
}

if ($item_id <= 0) {
    header("Location: " . $return_to . "?msg=" . urlencode("Invalid item."));
    exit();
}

$checkSql = "SELECT id, user_id, status, type FROM items WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $item_id);
$checkStmt->execute();
$itemResult = $checkStmt->get_result();

if ($itemResult->num_rows !== 1) {
    header("Location: " . $return_to . "?msg=" . urlencode("Item not found."));
    exit();
}

$item = $itemResult->fetch_assoc();

if ($item['status'] !== 'open') {
    header("Location: " . $return_to . "?msg=" . urlencode("This item cannot be claimed."));
    exit();
}

if ((int)$item['user_id'] === $claimant_id) {
    header("Location: " . $return_to . "?msg=" . urlencode("You cannot claim your own item."));
    exit();
}

$dupSql = "SELECT id FROM claims WHERE item_id = ? AND claimant_id = ? AND status = 'pending'";
$dupStmt = $conn->prepare($dupSql);
$dupStmt->bind_param("ii", $item_id, $claimant_id);
$dupStmt->execute();
$dupResult = $dupStmt->get_result();

if ($dupResult->num_rows > 0) {
    header("Location: " . $return_to . "?msg=" . urlencode("You already have a pending claim for this item."));
    exit();
}

$insertSql = "INSERT INTO claims (item_id, claimant_id, message, status) VALUES (?, ?, ?, 'pending')";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("iis", $item_id, $claimant_id, $message);

if ($insertStmt->execute()) {
    header("Location: " . $return_to . "?msg=" . urlencode("Claim request sent successfully."));
    exit();
}

header("Location: " . $return_to . "?msg=" . urlencode("Error: " . $conn->error));
exit();
