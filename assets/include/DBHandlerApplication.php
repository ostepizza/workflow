<?php
class DBHandlerApplication extends DBHandlerBase {
    // Creates a new application and returns the new application ID if successful. Else returns false.
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

    // Retrieves all applications from a user id. Returns an array with the applications if successful, else returns false.
    // TODO: This is broken, fix it
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

    // Retrieves all applications from a listing id. Returns an array with the applications if successful, else returns false.
    // Todo: Possibility to retrieve archived applications
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

    // Retrieves an application by application id. Returns an array with the application if successful, else returns false.
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