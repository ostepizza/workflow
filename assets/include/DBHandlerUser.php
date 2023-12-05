<?php
class DBHandlerUser extends DBHandlerBase {
    /*
        // Checks if an email is already present in the db. Returns true if it finds an email, and returns false if email is available.
        // Optional parameter userid can be used to exclude a user from the search, in case an email is being updated.
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

    /*
        Select userdata from DB from an email. Returns an array with the userdata if successful, else returns false.
        Normally intended to verify login details and set a user ID in Session.
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

    /*
        Retrieve all data fields for a user in the database. Used to display profile information for a specific user,
        like on Profile View or viewing a sent Job Application.
        Returns an array of information if user ID exists, else it returns false.
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

    // Retrieves a users ID from an email
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
        if ($location == '') {
            $location = NULL;
        }
        return $this->updateDetail($userId, 'location', $location);
    }

    //THIS METHOD IS UNTESTED, AS TYPE = DATE
    function updateBirthday($userId, $birthday) {
        if ($birthday == '') {
            $birthday = NULL;
        }
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

    function updatePassword($userId, $plainPassword) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        return $this->updateDetail($userId, 'password', $hashedPassword);
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

    //Function for search field by searching either for name, mail, or competence
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

    function updateProfileImage($userid, $fileName) {
        $sql = 'UPDATE `user` SET `picture` = ? WHERE `id` = ?;';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $fileName, $userid);

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