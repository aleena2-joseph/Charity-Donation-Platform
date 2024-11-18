<?php
session_start();
ob_start();
include 'db_connection.php';
ob_end_clean();

$statusMessage = $request = "";
if (isset($_SESSION['wallet_address'])) {
    $wallet_address = $_SESSION['wallet_address'];

    // Fetch request details
    $stmt = $conn->prepare("SELECT id, status, amount_needed FROM requests WHERE wallet_address = ?");
    $stmt->bind_param("s", $wallet_address);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if ($request) {
        $requestId = $request['id'];
        $currentStatus = $request['status'];

        if ($currentStatus === 'Approved') {
            $statusMessage = "Request Approved. Enter the amount to withdraw.";
        } elseif ($currentStatus === 'Completed') {
            $statusMessage = "Request already completed. Withdrawal is not possible.";
        } else {
            $statusMessage = "Your request is not yet approved.";
        }
    } else {
        $statusMessage = "No request found for this wallet address.";
    }
    $stmt->close();
} else {
    $statusMessage = "Wallet address not found in session.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {
    $withdrawAmount = floatval($_POST['amount']);
    if ($request && $currentStatus === 'Approved' && $withdrawAmount <= $request['amount_needed']) {
        // Mark as completed
        $updateStmt = $conn->prepare("UPDATE requests SET status = 'Completed' WHERE id = ?");
        $updateStmt->bind_param("i", $requestId);

        if ($updateStmt->execute()) {
            $statusMessage = "Withdrawal successful! The request has been marked as completed.";
            echo "<script>alert('Withdrawal completed successfully.');</script>";
        } else {
            $statusMessage = "Error updating request status. Please try again.";
        }

        $updateStmt->close();
    } else {
        $statusMessage = "Invalid withdrawal amount or request status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Withdraw Amount</title>
</head>
<body>
    <div class="container silver-box">
        <h1>Withdraw Amount</h1>
        <p>Status: <span style="color: blue;"><?php echo htmlspecialchars($statusMessage); ?></span></p>
        <?php if ($request && $currentStatus === 'Approved') { ?>
            <form method="POST">
                <label for="amount">Amount to Withdraw (ETH):</label>
                <input type="number" step="0.01" id="amount" name="amount" required><br><br>
                <button type="submit" name="withdraw" class="button">Withdraw</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
