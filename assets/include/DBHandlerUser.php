<?php
class DBHandlerUser extends DBHandlerBase {
    /**
     * Checks if an email is already present in the database.
     * @param string $email the email to check
     * @param int $userId (optional) the user id to exclude from the search
     * @return bool true if the email is taken, else false
     */
    function isEmailTaken($email, $userId = NULL) {
        // Make sure the email is always lowercase, for consistency
        $email = strtolower($email);

        // Set up the basic SQL query
        $sql = 'SELECT count(*) FROM `user` WHERE LOWER(`email`) = LOWER(?)';

        // If a user id is supplied, exclude this user from the search
        if ($userId !== NULL) {
            $sql .= ' AND NOT (`id` = ?)';
        }

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameteres based on whether a company id is supplied or not
        if ($userId !== NULL) {
            $stmt->bind_param('si', $email, $userId);
        } else {
            $stmt->bind_param('s', $email);
        }

        // Execute the statement, get results and close the statement
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // If more than 0 rows are returned, the name is taken and we return true.
            return true;
        } else {
            // If 0 rows are found, return false. The name is available.
            return false;
        }
    }

    /**
     * Selects user data from the database based on an email.
     * @param string $email the email to search for
     * @return array|false an array with user data if successful, else false
     */
    function selectUserByEmail($email) {
        // Make sure the email is always lowercase, for consistency
        $email = strtolower($email);

        // Select id, password and name from the email supplied. If it executes, the email is found in the DB.
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

    /**
     * Retrieve all data fields for a user in the database. Used to display profile information for a specific user,
     * like on Profile View or viewing a sent Job Application.
     * @param int $userId the user id to search for
     * @return array|false an array with user data if successful, else false
     */
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

    /**
     * Retrieves a users ID from an email
     * @param string $email the email to search for
     * @return int|false the user id if successful, else false
     */
    function getUserIdByEmail($email) {
        $sql = 'SELECT `id` FROM `user` WHERE `email` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                //User found
                $stmt->bind_result($userid);
                $stmt->fetch();
                return $userid;
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

    /**
     * Retrieves a users hashed password from the database
     * @param int $userId the user id to search for
     * @return string|false the hashed password if successful, else false
     */
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

    /**
     * Compares a plain-text password with a hashed password using the PHP function password_verify(). Returns true if passwords are same.
     * (This function is a wrapper for password_verify() to make it easier to use in other functions.)
     * @param string $inputPassword the plain-text password to compare
     * @param string $hashedPassword the hashed password to compare
     */
    function verifyPassword($inputPassword, $hashedPassword) {
        if (password_verify($inputPassword, $hashedPassword)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Logs in a user and stores session data. Checks if email exists, then compares passwords.
     * @param string $email the email to check
     * @param string $plainPassword the plain-text password to compare
     * @return bool true if successful login, else false
     */
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
                return true;
            } else {
                // Return false if password isn't verified
                return false;
            }
        } else {
            // Return false if no email is found in DB
            return false;
        }
    }

    /**
     * Adds a user to the database
     * @param string $email the email to add
     * @param string $plainPassword the plain-text password to add
     * @param string $firstname the first name to add
     * @param string $lastname the last name to add
     * @return bool true if successful, else false
     */
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

    /**
     * Updates a field in the user table
     * @param int $userId the user id to update
     * @param string $field the field to update
     * @param string $detail the detail to update the field to
     * @return bool true if successful, else false
     */
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

    /**
     * Updates the email field of a user
     * @param int $userId the user id to update
     * @param string $email the email to update to
     * @return bool true if successful, else false
     */
    function updateEmail($userId, $email) {
        return $this->updateDetail($userId, 'email', $email);
    }

    /**
     * Updates the first name field of a user
     * @param int $userId the user id to update
     * @param string $firstName the first name to update to
     * @return bool true if successful, else false
     */
    function updateFirstName($userId, $firstName) {
        return $this->updateDetail($userId, 'first_name', $firstName);
    }

    /**
     * Updates the last name field of a user
     * @param int $userId the user id to update
     * @param string $lastName the last name to update to
     * @return bool true if successful, else false
     */
    function updateLastName($userId, $lastName) {
        return $this->updateDetail($userId, 'last_name', $lastName);
    }

    /**
     * Updates the telephone field of a user
     * @param int $userId the user id to update
     * @param string $telephone the telephone to update to
     * @return bool true if successful, else false
     */
    function updateTelephone($userId, $telephone) {
        return $this->updateDetail($userId, 'telephone', $telephone);
    }

    /**
     * Updates the location field of a user
     * @param int $userId the user id to update
     * @param string $location the location to update to
     * @return bool true if successful, else false
     */
    function updateLocation($userId, $location) {
        if ($location == '') {
            $location = NULL;
        }
        return $this->updateDetail($userId, 'location', $location);
    }

    /**
     * Updates the birthday field of a user
     * @param int $userId the user id to update
     * @param string $birthday the birthday to update to
     * @return bool true if successful, else false
     */
    function updateBirthday($userId, $birthday) {
        if ($birthday == '') {
            $birthday = NULL;
        }
        return $this->updateDetail($userId, 'birthday', $birthday);
    }

    /**
     * Updates the competence field of a user
     * @param int $userId the user id to update
     * @param string $competence the competence to update to
     * @return bool true if successful, else false
     */
    function updateCompetence($userId, $competence) {
        return $this->updateDetail($userId, 'competence', $competence);
    }

    /**
     * Hashes and updates the password field of a user
     * @param int $userId the user id to update
     * @param string $plainPassword the password to update to
     * @return bool true if successful, else false
     */
    function updatePassword($userId, $plainPassword) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        return $this->updateDetail($userId, 'password', $hashedPassword);
    }

    /**
     * Toggles whether a user is set as searchable in the user table (sets searchable to 1 if it was 0, 0 if it was 1)
     * @param int $userId the user id to update
     * @return bool true if successful, else false
     */
    function toggleSearchable($userId) {
        // SQL statement to toggle searchable
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

    /**
     * Search for a user by name, email or competence
     * @param string $searchTerm the search term to search for
     * @return array|false an array with user data if successful, else false
     */
    function searchForUser($searchTerm) {
        //Prepare the SQL statement 
        $sql = "SELECT `first_name`, `last_name`, `email`, `competence`, `picture` 
                FROM `user` 
                WHERE (`first_name` LIKE ?
                OR `last_name` LIKE ?
                OR `email` LIKE ? 
                OR `competence` LIKE ?) 
                AND `searchable` = 1";
        $stmt = $this->conn->prepare($sql);

        //Binds the same paramaters with the fuction variable
        $searchTerm = "%$searchTerm%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        //If the statement executes, fetch results as an associative array and return the result
        if($stmt->execute()) {
                $result = $stmt->get_result();
                $users = $result->fetch_all(MYSQLI_ASSOC);

                $stmt->close();
                return $users;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Updates the profile image field of a user
     * @param int $userId the user id to update
     * @param string $fileName the file name to update to
     * @return bool true if successful, else false
     */
    function updateProfileImage($userId, $fileName) {
        $sql = 'UPDATE `user` SET `picture` = ? WHERE `id` = ?;';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $fileName, $userId);

        // If the statement successfully executes, return true. If something somehow goes wrong, return false.
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Updates the CV field of a user
     * @param int $userId the user id to update
     * @param string $fileName the file name to update to
     * @return bool true if successful, else false
     */
    function updateUserCV($userId, $fileName) {
        $sql = 'UPDATE `user` SET `cv` = ? WHERE `id` = ?;';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $fileName, $userId);

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