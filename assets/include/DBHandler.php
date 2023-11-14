<?php
include("connection.php");

class DBHandlerBase {
    protected $conn; //DB connection

    // Create a connection as the object is created
    function __construct() {
        $this->conn = $this->createDBConnection(); // Connects to the database
    }

    // Creates a connection to the Database
    protected function createDBConnection() {
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
}

class DBHandlerUser extends DBHandlerBase {
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

    function selectAllUserInfoByUserId($userId) {
        $sql = 'SELECT `email`, `password`, `first_name`, `last_name`, `telephone`, `location`, `birthday`, `picture`, `cv`, `searchable`, `competence` FROM `user` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);

        // Do a quick if, just in case something fails while executing the statement
        if ($stmt->execute()) {
            $stmt->store_result();
    
            if ($stmt->num_rows == 1) {
                // If one row is found, everything issa okay. Store the various data in variables.
                $stmt->bind_result($email, $hashedPassword, $firstName, $lastName, $telephone, $location, $birthday, $picture, $cv, $searchable, $competence);
                $stmt->fetch();
                $stmt->close();

                // Return an array with some user data. user_id is typically used for all session management.
                return array(
                    'email' => $email,
                    'hashedPassword' => $hashedPassword,
                    'firstName' => $firstName,
                    'lastName' => $lastName, 
                    'telephone' => $telephone,
                    'location' => $location,
                    'birthday' => $birthday,
                    'picture' => $picture,
                    'cv' => $cv,
                    'searchable' => $searchable,
                    'competence' => $competence
                );
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

    // Retrieves a users hashed password from the database
    function getUserPasswordByUserId($userId) {
        $sql = 'SELECT `password` FROM `user` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                //User found
                $stmt->bind_result($hashedPassword);
                $stmt->fetch();
                return $hashedPassword;
            } else {
                //Return false if no user found
                $stmt->close();
                return false;
            }
        } else {
            // Return false if statement didn't execute.
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
        if ($user_details = $this->selectUserByEmail($email)) {
            // If selectUserByEmail returns anything, we have $user_details
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

    // Update a field using a user id input, what field to update, and what to update it to
    function updateDetail($userId, $field, $detail) {
        //UPDATE `user` SET `'.$field.'` = ? WHERE `id` = ?;
        // Prepare and execute sql statement to add user with supplied info
        $sql = 'UPDATE `user` SET `' . $field . '` = ? WHERE `id` = ?;';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $detail, $userId);

        // If the statement successfully executes, return true. If something somehow goes wrong, return false.
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Update email field of a user
    function updateEmail($userId, $email) {
        return $this->updateDetail($userId, 'email', $email);
    }

    // Update first_name field of a user
    function updateFirstName($userId, $firstName) {
        return $this->updateDetail($userId, 'first_name', $firstName);
    }

    // Update last_name field of a user
    function updateLastName($userId, $lastName) {
        return $this->updateDetail($userId, 'last_name', $lastName);
    }

    // Update telephone field of a user
    function updateTelephone($userId, $telephone) {
        return $this->updateDetail($userId, 'telephone', $telephone);
    }

    // Update location field of a user
    function updateLocation($userId, $location) {
        return $this->updateDetail($userId, 'location', $location);
    }

    //THIS METHOD IS UNTESTED, AS TYPE = DATE
    function updateBirthday($userId, $birthday) {
        return $this->updateDetail($userId, 'birthday', $birthday);
    }

    //THIS METHOD IS UNTESTED, AS TYPE = BLOB
    function updatePicture($userId, $picture) {
        return $this->updateDetail($userId, 'picture', $picture);
    }

    //THIS METHOD IS UNTESTED, AS TYPE = BLOB
    function updateCV($userId, $cv) {
        return $this->updateDetail($userId, 'cv', $cv);
    }

    // Update competence field of a user
    function updateCompetence($userId, $competence) {
        return $this->updateDetail($userId, 'competence', $competence);
    }

    function updatePassword($userId, $hashedPassword) {
        // update a users password with a user id and prehashed password
    }

    // Toggles whether a user is set as searchable in the user table (sets searchable to 1 if it was 0, 0 if it was 1)
    function toggleSearchable($userId) {
        $sql = '
            UPDATE `user`
            SET searchable = CASE
                WHEN searchable = 0 THEN 1
                WHEN searchable = 1 THEN 0
            END
            WHERE `id` = ?;
            ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);

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

class DBHandlerCompany extends DBHandlerBase {
    /*
        Function to retrieve a company id from a user id.
        Can be used to just check if a user is a part of any company as well.
        Returns the companyid if found,
        else it returns false.
    */
    function getCompanyIdFromUserId($userid) {
        // Ask database if logged in member is found in the company_management table
        $sql = 'SELECT `company_id` FROM `company_management` WHERE `user_id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userid);
        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If any rows are found, the user is a part of a company. Return the company id
                $stmt->bind_result($companyid);
                $stmt->fetch();
                return $companyid;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            // Return false just in case SQL statement didn't execute properly
            $stmt->close();
            return false;
        }
    }

    function isUserCompanySuperuser($userId) {

        $sql = 'SELECT `superuser` FROM `company_management` WHERE `user_id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                // If one row is found, the user is a part of a company
                $stmt->bind_result($superuser);
                $stmt->fetch();
                $stmt->close();

                // Return $superuser directly, as it's either 1 or 0, aka true or false
                return $superuser;
            } else {
                $stmt->close();
                return false;
            }
            
        } else {
            // Return false just in case SQL statement didn't execute properly
            $stmt->close();
            return false;
        }
    }

    /*
        Function to retrieve company name and description from a company id.
        Can be used to just check if a company with id n exists as well.
        Returns an array with the company name and description if found,
        else it returns false.
    */
    function getCompanyDetailsFromCompanyId($companyId) {
        $sql = 'SELECT `name`, `description` FROM `company` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                // Retrieve the company name and description from database
                $stmt->bind_result($companyName, $companyDescription);
                $stmt->fetch();
                $stmt->close();

                // If the company has no description (NULL or blank), return a placeholder description:
                if ($companyDescription == NULL && $companyDescription == '') {
                    $companyDescription = 'This company has no description.';
                }

                // Return an array with the company name and description
                return array($companyName, $companyDescription);
            } else {
                // If for some reason this has been called with a wrong company id, return false
                $stmt->close();
                return false;
            }
        }
        // Return false just in case SQL statement didn't execute properly
        $stmt->close();
        return false;
    }

    // Returns an array with company name and description if user is a part of a company. Else, returns false.
    function getCompanyDetailsFromUserId($userId) {
        $companyId = $this->getCompanyIdFromUserId($userId);
        return $companyDetails = $this->getCompanyDetailsFromCompanyId($companyId);
    }

    function updateCompanyDetailWithCompanyId($companyId, $detail, $updatedDetail) {
        $sql = 'UPDATE `company` SET `' . $detail . '` = ? WHERE `company`.`id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $updatedDetail, $companyId);
        if ($stmt->execute()) {
            // If the statement executes no problem, the detail should be updated
            $stmt->close();
            return true;
        } else {
            // Return false just in case SQL statement didn't execute properly
            $stmt->close();
            return false;
        }
    }

    function updateCompanyNameWithCompanyId($companyId, $newCompanyName) {
        return $this->updateCompanyDetailWithCompanyId($companyId, 'name', $newCompanyName);
    }

    function updateCompanyDescriptionWithCompanyId($companyId, $newCompanyDescription) {
        return $this->updateCompanyDetailWithCompanyId($companyId, 'description', $newCompanyDescription);
    }

    function deleteCompanyById($companyId) {
        $sql = 'DELETE FROM `company` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>