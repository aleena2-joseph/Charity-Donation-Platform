<?php
session_start();


ob_start();
include 'db_connection.php';
ob_end_clean(); 


$query = "SELECT * FROM transaction_history";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Transaction History</title>
</head>
<body>
    <div class="container silver-box">
        <h1 style="color:green">Transaction History</h1>
        <table class="donation-table">
            <thead>
                <tr>
                    <th>Receiver Address</th>
                    <th>Amount Donated (ETH)</th>
                    <th>Transaction Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['receiver_address']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td><?php echo $row['transaction_status']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <a href="index.php" class="button">Home</a>
    </div>
</body>
</html>
