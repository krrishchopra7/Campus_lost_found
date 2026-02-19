<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

$sql = "SELECT claims.id, claims.status, users.name, items.item_name
        FROM claims
        JOIN items ON claims.item_id = items.id
        JOIN users ON claims.claimant_id = users.id
        WHERE items.user_id = ?";



$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Claim Requests</h2>

<?php while ($row = $result->fetch_assoc()) { ?>
    <div style="border:1px solid black; padding:10px; margin:10px;">
        <p><b>Item:</b> <?php echo $row['item_name']; ?></p>

        <p><b>Claimed By:</b> <?php echo $row['name']; ?></p>
        <p><b>Status:</b> <?php echo $row['status']; ?></p>

        <?php if ($row['status'] == 'pending') { ?>
            <a href="update_claim.php?id=<?php echo $row['id']; ?>&action=approved">Approve</a>
            |
            <a href="update_claim.php?id=<?php echo $row['id']; ?>&action=rejected">Reject</a>
        <?php } ?>
    </div>
<?php } ?>
