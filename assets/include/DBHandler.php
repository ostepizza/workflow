<?php
include("connection.php");

class DBHandler {
    private $conn; //DB connection

    // Create a connection as the object is created
    function __construct() {
        $this->conn = $this->createDBConnection(); // Connects to the database
    }

    // Creates a connection to the Database
    private function createDBConnection() {
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

    // Checks if an email is already present in the db. Returns true if it finds an email, and returns false if email is available.
    function isEmailTaken($email) {
        // Make sure the email is always lowercase, for consistency
        $email = strtolower($email);

        /*
            This SQL statement checks whether the email is already in the database or not.
            If it returns more than 0 rows, then it exists.
        */
        $sql = 'SELECT count(*) FROM `user` WHERE `email` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // If more than zero emails are found, the email is taken
        if ($count > 0) {
            // Return true since email is already present in DB
            return true;
        } else {
            // Return false since email is available
            return false;
        }
    }

    // Adds a user to the DB. Returns true if successful, else false.
    function addUserToDB($email, $plainPassword, $firstname, $lastname) {
        // Make sure the email is always lowercase, for consistency
        $email = strtolower($email);

        // Hash the plaintext password
        $hashed_password = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Convert first and last name to First Name Lastname for consistency
        $firstname = strtolower($firstname);
        $firstname = ucwords($firstname);

        $lastname = strtolower($lastname);
        $lastname = ucwords($lastname);

        // Prepare and execute sql statement to add user with supplied info
        $sql = 'INSERT INTO `user` (`email`, `password`, `first_name`, `last_name`) VALUES (?, ?, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssss', $email, $hashed_password, $firstname, $lastname);

        // If the statement successfully executes, return true. If something somehow goes wrong, return false.
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}
?>