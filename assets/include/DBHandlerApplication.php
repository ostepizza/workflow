<?php
class DBHandlerApplication extends DBHandlerBase {

    /**
     * Creates a new application. Returns the application id if successful, else returns false.
     * @param int $listingId id of the listing the application is for
     * @param int $userId id of the user that created the application
     * @return int|false The id of the created application
     */
    function createNewApplication($listingId, $userId) {
        // Create a new empty application
        $sql = 'INSERT INTO `job_application` (`job_listing_id`, `user_id`) VALUES (?, ?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $listingId, $userId);

        // If the statement executes, return the application id. Else return false.
        if ($stmt->execute()) {
            $applicationId = $this->conn->insert_id;
            $stmt->close();
            return $applicationId;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Retrieves all applications from a user id. Returns an array with the applications if successful, else returns false.
     * @param int $userId id for the user
     * @return array|false Arrays containing information about applications
     */ 
    function getAllUserApplications($userId) {
        $sql = 'SELECT ja.*, jl.name as listing_name, jl.company_id, jl.deadline, jl.published, jl.views, c.name as company_name, c.description as company_description
            FROM `job_application` ja
            JOIN `job_listing` jl ON ja.job_listing_id = jl.id
            JOIN `company` c ON jl.company_id = c.id
            WHERE ja.`user_id` = ? 
            ORDER BY jl.`published` DESC, jl.`deadline` IS NULL ASC, jl.`deadline` ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $applications = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $applications;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Gets all the applications per listing
     * @param int $listingId id for the listing
     * @param bool $archived True if the applications retrieved should be archived, else false
     * @param bool $pinned True if the applications retrieved should be pinned, else false
     * @return array|false Information about the applications stored in arrays
     */
    function getAllListingApplications($listingId, $archived = false, $pinned = false) {
        // Retrieve all applications from a listing id, with all the user information
        $sql = 'SELECT ja.*, u.first_name, u.last_name, u.email, u.telephone, u.location, u.birthday, u.picture, u.cv, u.competence, c.name as company_name
            FROM `job_application` ja
            JOIN `user` u ON ja.user_id = u.id
            JOIN `job_listing` jl ON ja.job_listing_id = jl.id
            JOIN `company` c ON jl.company_id = c.id
            WHERE ja.`job_listing_id` = ? AND ja.`sent` = 1';

        // Handle the archived and pinned parameters
        if ($archived) {
            $sql .= ' AND ja.`archived` = 1';
        } else {
            $sql .= ' AND ja.`archived` = 0';
        }

        if ($pinned) {
            $sql .= ' AND ja.`pinned` = 1';
        } elseif (!$archived) {
            $sql .= ' AND ja.`pinned` = 0';
        }

        // Finish the SQL query with an ORDER BY statement
        $sql .= ' ORDER BY ja.`sent_datetime` DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $applications = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $applications;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Retrieves an application by application id.
     * @param int $applicationId for the application
     * @return array|false  information about an application in an array, or false if something goes wrong
     */
    function getApplication($applicationId) {
        $sql = 'SELECT ja.*, 
            u.first_name, u.last_name, u.email, u.telephone, u.location, u.birthday, u.picture, u.cv, u.competence, 
            c.id as company_id, c.name as company_name, c.description as company_description, 
            jl.name as listing_name
            FROM `job_application` ja
            JOIN `user` u ON ja.user_id = u.id
            JOIN `job_listing` jl ON ja.job_listing_id = jl.id
            JOIN `company` c ON jl.company_id = c.id
            WHERE ja.`id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $applicationId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $application = $result->fetch_assoc();
            $stmt->close();
            return $application;
        } else {
            $stmt->close();
            return false;
        }
    }

     /** 
     * Updates either an empty or already filled application. Doesn't send the application
     * @param int $applicationId ID for application that gets updated
     * @param string $title Title for the application
     * @param string $description Description for the application
     * @return bool True if successful, else false
     */
    function updateApplicationContent($applicationId, $title, $description) {
        $sql = 'UPDATE `job_application` SET `title` = ?, `text` = ? WHERE `job_application`.`id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssi', $title, $description, $applicationId);

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
     * Updates either an empty or already filled application with a date for when it was sent
     * @param int $applicationId ID for application that gets updated
     * @return bool True if successful, else false
     */
    function sendApplication($applicationId) {
        $sql = 'UPDATE `job_application` SET `sent` = 1, `sent_datetime` = NOW() WHERE `job_application`.`id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $applicationId);

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
     * Checks an application up to a company
     * @param int $applicationId ID for application that gets checked
     * @param int $companyId ID for company that the application gets checked against
     * @return bool True if successful, else false
     */
    function checkApplicationIdAndCompany($applicationId, $companyId) {
        $sql = 'SELECT ja.*, jl.company_id
            FROM `job_application` ja
            JOIN `job_listing` jl ON ja.job_listing_id = jl.id
            WHERE ja.`id` = ? AND jl.`company_id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $applicationId, $companyId);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
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
     * Toggles an application as archived in DB
     * @param int $applicationId ID for application that gets archived
     * @return bool True if toggled successfully or false if not
     */
    function toggleApplicationArchived($applicationId) {
        $sql = '
            UPDATE `job_application`
            SET archived = CASE
                WHEN archived = 0 THEN 1
                WHEN archived = 1 THEN 0
            END
            WHERE `id` = ?;
            ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $applicationId);

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
     * Toggles an application as pinned in the DB
     * @param int $applicationId The id of the application that is going to be set as pinned
     * @return bool True if toggled successfully or false if not
    */
    function toggleApplicationPinned($applicationId) {
        $sql = '
            UPDATE `job_application`
            SET pinned = CASE
                WHEN pinned = 0 THEN 1
                WHEN pinned = 1 THEN 0
            END
            WHERE `id` = ?;
            ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $applicationId);

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
     * Get the amount of applications received for a listing
     * @param int $listingId The id of the listing
     * @return int|false The amount of applications received, or false if something goes wrong
     */
    function getListingApplicationCount($listingId) {
        $sql = 'SELECT COUNT(*) as applications_received FROM `job_application` WHERE `job_listing_id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $applicationsReceived = $result->fetch_assoc()['applications_received'];
            $stmt->close();
            return $applicationsReceived;
        } else {
            $stmt->close();
            return false;
        }
    }
}
?>