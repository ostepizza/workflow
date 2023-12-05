<?php
class DBHandlerCompany extends DBHandlerBase {

    /**
     * Creates a new company with a name, description, and the id of the user requesting it.
     * @param string $name the name of the company
     * @param string $description the description of the company
     * @param int $userId the id of the user requesting the company
     * @return bool true if successful, else returns false.
     */
    function createNewCompany($name, $description, $userId) {
        // Prepare, bind and execute the SQL statement
        $sql = 'INSERT INTO `company` (`id`, `name`, `description`) VALUES (NULL, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $name, $description);
        if ($stmt->execute()) {
            // If the statement has executed, part two begins. Start by closing the previous statement.
            $stmt->close();

            // Retrieve the ID of the newly inserted company
            $companyId = $this->conn->insert_id;

            // Prepare a statement with the user id and company id to insert into company_management. 
            // (Superuser is set to 1, as the user creating the company is automatically a superuser.)
            $sql = 'INSERT INTO `company_management` (`user_id`, `company_id`, `superuser`) VALUES (?, ?, 1)';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ii', $userId, $companyId);

            if ($stmt->execute()) {
                /*
                    If all of these SQL statements have been executed successfully,
                    the company is now properly stored in the database,
                    with correct references to their proper foreign keys
                */
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Retrieves the company id from a user id.
     * Can be used to just check if a user is a part of any company as well.
     * @param int $userid the id of the user
     * @return int the id of the company the user is a part of (if found), else returns false.
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
                // Return false if user isn't a part of a company
                $stmt->close();
                return false;
            }
        } else {
            // Return false just in case SQL statement didn't execute properly
            $stmt->close();
            return false;
        }
    }

    /**
     * Checks if a user is a superuser of a company.
     * @param int $userId the id of the user
     * @return bool true if user is a superuser, else returns false.
     */
    function isUserCompanySuperuser($userId) {
        // Ask database if logged in member is found in the company_management table
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

    /**
     * Toggles whether a company user is a superuser or not.
     * @param int $companyId the id of the company
     * @param int $userId the id of the user
     * @return bool true if successful, else returns false.
     */
    function toggleUserSuperuser($companyId, $userId) {
        $sql = '
            UPDATE `company_management`
            SET superuser = CASE
                WHEN superuser = 0 THEN 1
                WHEN superuser = 1 THEN 0
            END
            WHERE `company_id` = ? AND `user_id` = ?;
            ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $companyId, $userId);

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
     * Retrieves company name and description from a company id.
     * Can be used to just check if a company with id n exists as well.
     * @param int $companyId the id of the company
     * @return array an array with the company name and description if found, else it returns false.
     */
    function getCompanyDetailsFromCompanyId($companyId) {
        $sql = 'SELECT `id`, `name`, `description` FROM `company` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                
                // Retrieve the company name and description from database
                $stmt->bind_result($companyId, $companyName, $companyDescription);
                $stmt->fetch();
                $stmt->close();

                // If the company has no description (NULL or blank), return a placeholder description:
                if ($companyDescription == NULL && $companyDescription == '') {
                    $companyDescription = 'This company has no description.';
                }

                // Return an array with the company name and description
                return array('companyId' => $companyId,'companyName' => $companyName, 'companyDescription' => $companyDescription);
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

    /**
     * Retrieves company name and description from a user id.
     * Can be used to just check if a user is a part of any company as well.
     * @param int $userId the id of the user
     * @return array|false an array with the company name and description if found, else it returns false.
     */
    function getCompanyDetailsFromUserId($userId) {
        $companyId = $this->getCompanyIdFromUserId($userId);
        return $companyDetails = $this->getCompanyDetailsFromCompanyId($companyId);
    }

    /**
     * Checks if a company name is already taken.
     * @param string $name the name to check
     * @param int $companyId (optional) id of a company company to exclude
     * @return bool true if the name is taken, else returns false.
     */
    function isCompanyNameTaken($name, $companyId = NULL) {
        // Set up the basic SQL query
        $sql = 'SELECT * FROM `company` WHERE LOWER(`name`) = LOWER(?)';

        // If a company id is supplied, exclude this company from the search
        if ($companyId !== NULL) {
            $sql .= ' AND NOT (`id` = ?)';
        }

        // Prepare the statement
        $stmt = $this->conn->prepare($sql);

        // Bind the parameteres based on whether a company id is supplied or not
        if ($companyId !== NULL) {
            $stmt->bind_param('si', $name, $companyId);
        } else {
            $stmt->bind_param('s', $name);
        }

        // Execute the statement, get results and close the statement
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // If more than 0 rows are returned, the name is taken and we return true.
            return true;
        } else {
            // If 0 rows are found, return false. The name is available.
            return false;
        }
    }

    /**
     * Updates a company detail using the company id, what detail to update and what value to update it to.
     * @param int $companyId the id of the company
     * @param string $detail the detail to update
     * @param string $updatedDetail the value to update the detail to
     * @return bool true if successful, else returns false.
     */
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

    /**
     * Updates a company name using company id and the new name
     * @param int $companyId the id of the company
     * @param string $newCompanyName the new name of the company
     * @return bool true if successful, else returns false.
     */
    function updateCompanyNameWithCompanyId($companyId, $newCompanyName) {
        return $this->updateCompanyDetailWithCompanyId($companyId, 'name', $newCompanyName);
    }

    /**
     * Updates a company description using company id and the new description
     * @param int $companyId the id of the company
     * @param string $newCompanyDescription the new description of the company
     * @return bool true if successful, else returns false.
     */
    function updateCompanyDescriptionWithCompanyId($companyId, $newCompanyDescription) {
        return $this->updateCompanyDetailWithCompanyId($companyId, 'description', $newCompanyDescription);
    }

    /**
     * Deletes a company using the company id.
     * @param int $companyId the id of the company
     * @return bool true if successful, else returns false.
     */
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

    /**
     * Adds a new user to a company, with the users email and a company id.
     * @param string $email the email of the user
     * @param int $companyId the id of the company
     * @return bool true if successful, else returns false.
     */
    function addNewUserToCompany($email, $companyId) {
        // Create a new DBHandlerUser object to retrieve the user id from the email, then destroy the object
        $dbhu = new DBHandlerUser();
        $userId = $dbhu->getUserIdByEmail($email);
        unset($dbhu);

        // If the user id is not already in a company, add the user to the company. Else return false.
        if(!$this->getCompanyIdFromUserId($userId)) {
            $sql = 'INSERT INTO `company_management` (`user_id`, `company_id`, `superuser`) VALUES (?, ?, 0)';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ii', $userId, $companyId);

            // If the statement successfully executes, return true. If something somehow goes wrong, return false.
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Removes a user from a company, using the company id and the user id.
     * @param int $companyId the id of the company
     * @param int $userId the id of the user
     * @return bool true if successful, else returns false.
     */
    function removeUserFromCompany($companyId, $userId) {
        $sql = 'DELETE FROM `company_management` WHERE `company_id` = ? AND `user_id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $companyId, $userId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieves all users from a company, using the company id.
     * @param int $companyId the id of the company
     * @return array|false an array with all the users if successful, else returns false.
     */
    function retrieveAllCompanyUsers($companyId) {
        // Select userdata from DB from the company ID.
        $sql = 'SELECT u.id, u.first_name, u.last_name, u.email, cm.superuser
                FROM user u
                JOIN company_management cm ON u.id = cm.user_id
                JOIN company c ON c.id = cm.company_id
                WHERE c.id = ?
                ORDER BY cm.superuser DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);

        // If the statement executes, return the array of userinfo. Else return false.
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $users;
        } else {
            $stmt->close();
            return false;
        }
    }
}
?>