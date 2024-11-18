<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


$servername = "localhost";  
$username = "root";         
$password = "";             
$database = "charity_connect";  


$conn = mysqli_connect($servername, $username, $password);


if (!$conn) {
   
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected to the database server successfully!<br>";
}


$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS $database";
if (mysqli_query($conn, $sqlCreateDB)) {
    echo "Database '$database' created or already exists.<br>";
} else {
    die("Error creating database: " . mysqli_error($conn));
}


if (!mysqli_select_db($conn, $database)) {
    die("Error selecting database: " . mysqli_error($conn));
} else {
    echo "Database '$database' selected successfully.<br>";
}


$sqlCreateTable = "
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receiver_name VARCHAR(255) NOT NULL,
    wallet_address VARCHAR(255) NOT NULL,
    reason TEXT NOT NULL,
    amount_needed FLOAT NOT NULL,
    id_proof VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending'
)";

if (mysqli_query($conn, $sqlCreateTable)) {
    echo "Table 'requests' created or already exists.<br>";
} else {
    die("Error creating table: " . mysqli_error($conn));
}



?>