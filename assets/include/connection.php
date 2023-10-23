<?php
function createDBConnection (){
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "workflowdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection to the database failed: " . $conn->connect_error);
}
    mysqli_set_charset($conn, "utf8");
    return $conn;
}