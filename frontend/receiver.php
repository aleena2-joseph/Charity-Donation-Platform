<?php
session_start();
ob_start();
include 'db_connection.php';
ob_end_clean();

// Fetch wallet address from session
$statusMessage = "";
$statusRow = null;

if (isset($_SESSION['wallet_address'])) {
    $wallet_address = $_SESSION['wallet_address'];

    // Fetch request status
    $stmt = $conn->prepare("SELECT status FROM requests WHERE wallet_address = ?");
    $stmt->bind_param("s", $wallet_address);
    $stmt->execute();
    $result = $stmt->get_result();
    $statusRow = $result->fetch_assoc();

    if ($statusRow) {
        if ($statusRow['status'] === 'Approved') {
            $statusMessage = "Approved. You can proceed with withdrawal.";
        } else {
            $statusMessage = "Your request is not approved yet. Please wait for donor approval.";
        }
    } else {
        $statusMessage = "No request found for this wallet address.";
    }
    $stmt->close();
} else {
    $statusMessage = "Wallet address not found in session.";
}

// Handle new request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRequest'])) {
    $receiverName = $_POST['receiver_name'];
    $walletAddress = $_POST['wallet_address'];
    $reason = $_POST['reason'];
    $amountNeeded = $_POST['amount_needed'];
    $aadhaarNumber = $_POST['aadhaar_card'];

    $stmt = $conn->prepare("INSERT INTO requests (receiver_name, wallet_address, reason, amount_needed, id_proof, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("sssss", $receiverName, $walletAddress, $reason, $amountNeeded, $aadhaarNumber);

    if ($stmt->execute()) {
        $statusMessage = "Request submitted successfully.";
    } else {
        $statusMessage = "Error submitting request: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <title>Receiver Dashboard</title>
</head>
<body>
    <div class="container silver-box">
        <h1>Receiver Request Form</h1>
        
        <!-- Connect MetaMask -->
        <button id="connectMetaMask" class="button">Connect MetaMask</button>
        <p id="walletStatus" style="color: green; margin-top: 10px;"></p>

        <form method="POST">
            <label for="receiver_name">Receiver Name:</label>
            <input type="text" id="receiver_name" name="receiver_name" required><br><br>
            <label for="wallet_address">Wallet Address:</label>
            <input type="text" id="wallet_address" name="wallet_address" readonly required><br><br>
            <label for="reason">Reason:</label>
            <textarea id="reason" name="reason" rows="4" required></textarea><br><br>
            <label for="amount_needed">Amount Needed (ETH):</label>
            <input type="number" step="0.01" id="amount_needed" name="amount_needed" required><br><br>
            <label for="aadhaar_card">Aadhar Card (ID Proof):</label>
            <input type="text" id="aadhaar_card" name="aadhaar_card" required><br><br>
            <p>Status: <span style="color: blue;"><?php echo htmlspecialchars($statusMessage); ?></span></p>
            <button type="submit" name="submitRequest" class="button">Submit Request</button>
            <a href="index.php" class="button">Home</a>
        </form>

        <?php if ($statusRow && $statusRow['status'] === 'Approved') { ?>
            <form method="POST" action="receiveramt.php">
                <button type="submit" name="withdraw" class="button">Withdraw</button>
            </form>
        <?php } ?>
        
    </div>

    <script>
        document.getElementById('connectMetaMask').addEventListener('click', async () => {
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    const walletAddress = accounts[0];
                    document.getElementById('wallet_address').value = walletAddress;
                    document.getElementById('walletStatus').textContent = "Connected Wallet: " + walletAddress;
                } catch (error) {
                    alert("Could not connect to MetaMask. Please try again.");
                }
            } else {
                alert("MetaMask is not installed. Please install MetaMask to proceed.");
            }
        });
    </script>
</body>
</html>
