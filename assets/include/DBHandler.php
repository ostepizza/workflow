<?php
class DBHandlerBase {
    protected $conn; //DB connection

    /**
     * Constructor for the DBHandlerBase class
     * It creates a connection to the database
     */
    function __construct() {
        $this->conn = $this->createDBConnection(); // Connects to the database
    }

    /**
     * Creates a connection to the database
     */
    protected function createDBConnection() {
        $serverip = "localhost";
        $username = "root";
        $password = "";
        $dbname = "workflowdb";

        // Create connection
        $conn = new mysqli($serverip, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection to the database failed: " . $conn->connect_error);
        }

        // Set charset to utf8
        mysqli_set_charset($conn, "utf8");

        // Return the connection
        return $conn;
    }
}

// Include child classes
include_once('DBHandlerUser.php');
include_once('DBHandlerCompany.php');
include_once('DBHandlerListing.php');
include_once('DBHandlerApplication.php');
include_once('DBHandlerStatistics.php');

?>