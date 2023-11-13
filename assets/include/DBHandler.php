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

    // Select userdata from DB from an email. Returns an array with the userdata if successful, else returns false.
    function selectUserByEmail($email) {
        // Make sure the email is always lowercase, for consistency
        $email = strtolower($email);

        // Select userdata with the email supplied. If it executes, the email is found in the DB.
        $sql = 'SELECT `id`, `password`, `first_name`, `last_name` FROM `user` WHERE `email` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);

        // Do a quick if, just in case something fails while executing the statement
        if ($stmt->execute()) {
            $stmt->store_result();
    
            if ($stmt->num_rows == 1) {
                // If one row is found, everything issa okay. Store the various data in variables.
                $stmt->bind_result($user_id, $hashed_password, $user_fname, $user_lname);
                $stmt->fetch();
                $stmt->close();

                // Return an array with some user data. user_id is typically used for all session management.
                return array($user_id, $hashed_password, $user_fname, $user_lname);
            } else {
                // Return false if not found 1 row exactly. 0 rows = email not tied to account. More than 1 row = something's very wrong in the DB.
                $stmt->close();
                return false;
            }
        } else {
            // Return if statement didn't execute.
            $stmt->close();
            return false;
        }
    }

    // Compares a plain-text password with a hashed password using the PHP function password_verify(). Returns true if passwords are same.
    function verifyPassword($inputPassword, $hashedPassword) {
        if (password_verify($inputPassword, $hashedPassword)) {
            return true;
        } else {
            return false;
        }
    }

    // Used to log in and store session data. Checks if email exists, then compares passwords. If successful it returns nothing, but if somethings wrong it returns false.
    function logInUser($email, $plainPassword) {
        // Since selectUserByEmail either returns false or an array, we can first make sure that it doesn't return false:
        if ($this->selectUserByEmail($email) != false) {
            // If it doesn't return false, select again. This time we know for sure we get an array in return.
            $user_details = $this->selectUserByEmail($email);

            // Use the hashed password in the array to compare with supplied password
            if ($this->verifyPassword($plainPassword, $user_details[1])) {
                // If all is successful, take data from the $user_details array and put it in session
                $_SESSION['user_id'] = $user_details[0];
                $_SESSION['user_fname'] = $user_details[2];
                $_SESSION['user_lname'] = $user_details[3];
            } else {
                // Return false if password isn't verified
                return false;
            }
        } else {
            // Return false if no email is found in DB
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