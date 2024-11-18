<?php
session_start();
ob_start();
include 'db_connection.php';
ob_end_clean();

// Fetch pending requests
$query = "SELECT * FROM requests WHERE status = 'Pending'";
$result = $conn->query($query);

// Approve/Donate logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $requestId = intval($_POST['request_id']);
    $action = $_POST['action']; // 'approve_donate' or 'reject'

    if ($action === 'approve_donate') {
        // Fetch request details
        $stmt = $conn->prepare("SELECT * FROM requests WHERE id = ?");
        $stmt->bind_param('i', $requestId);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($request) {
            $receiverWallet = $request['wallet_address'];
            $amount = $request['amount_needed'];

            // Approve the request
            $updateQuery = "UPDATE requests SET status = 'Approved' WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('i', $requestId);

            if ($stmt->execute()) {
                // Add transaction to transaction_history
                $insertTransaction = "INSERT INTO transaction_history (receiver_address, amount, transaction_status) VALUES (?, ?, 'Completed')";
                $stmt = $conn->prepare($insertTransaction);
                $stmt->bind_param('sd', $receiverWallet, $amount);

                if ($stmt->execute()) {
                    echo "<script>alert('Donation completed successfully.');</script>";
                    header("Location: amthistory.php");
                    exit();
                } else {
                    echo "<script>alert('Error logging the transaction.');</script>";
                }
            } else {
                echo "<script>alert('Error approving the request.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Request not found.');</script>";
        }
    } elseif ($action === 'reject') {
        // Reject the request
        $updateQuery = "UPDATE requests SET status = 'Rejected' WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $requestId);

        if ($stmt->execute()) {
            echo "<script>alert('Request has been rejected successfully.');</script>";
            header("Refresh:0"); // Refresh the page to reflect changes
        } else {
            echo "<script>alert('Error rejecting request.');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Donor Dashboard</title>
</head>
<body>
    <div class="container silver-box">
        <h1 style="color:green">Welcome, Donor!</h1>
       
        <button id="connectMetaMask" class="button">Connect MetaMask</button>
        <p id="walletAddress" style="color:blue; margin-top:10px;"></p>

        <h2 style="color:green">Pending Requests</h2>
        <table class="donation-table">
            <thead>
                <tr>
                    <th>Receiver Name</th>
                    <th>Amount Needed (ETH)</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['amount_needed']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="action" value="approve_donate" class="button">Approve/Donate</button>
                                <button type="submit" name="action" value="reject" class="button" style="background-color: red;">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('connectMetaMask').addEventListener('click', async () => {
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    const walletAddress = accounts[0];
                    document.getElementById('walletAddress').textContent = "Connected Wallet: " + walletAddress;
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