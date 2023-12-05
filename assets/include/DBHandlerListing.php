<?php
class DBHandlerListing extends DBHandlerBase {

    /**
     * Function to get all active listings
     * @return array|false with all the active listings or false if something goes wrong
     */
    function getAllActiveListings() {
        $sql = 'SELECT jl.*, c.name as company_name, jc.title as category_title
            FROM `job_listing` jl
            JOIN `company` c ON jl.company_id = c.id
            LEFT JOIN `job_category` jc ON jl.job_category_id = jc.id
            WHERE jl.`published` = 1 AND DATE(jl.`deadline`) >= CURDATE()
            ORDER BY jl.`deadline` ASC';
        $stmt = $this->conn->prepare($sql);

        // If the statement executes, return an array with all the listings. Else return false.
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $listings = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $listings;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Function to get all listings from a company
     * @param int The id of the company
     * @return array|false with all the listings from a company or false if something goes wrong
     */
    function getAllCompanyListings($companyId) {
        $sql = 'SELECT jl.*, c.name as company_name, jc.title as category_title
            FROM `job_listing` jl
            JOIN `company` c ON jl.company_id = c.id
            LEFT JOIN `job_category` jc ON jl.job_category_id = jc.id
            WHERE jl.`company_id` = ? 
            ORDER BY jl.`published` DESC, jl.`deadline` IS NULL ASC, jl.`deadline` ASC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $listings = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $listings;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Function to search for listings matching a filter word and an array of category IDs
     * @param string The filter word
     * @param array The array of category IDs
     * @return array|false with all the listings matching the filter word and category IDs or false if something goes wrong
     */
    function searchListings($filterWord, $categoryIds) {
        // Add % to the beginning and end of the filter word, so it can match words that contain the filter word
        $filterWord = "%" . $filterWord . "%";

        // Check if the categoryIds array is empty
        if(empty($categoryIds)) {
            // If it is, modify the SQL query to not include the IN clause
            $sql = "SELECT jl.*, c.name as company_name, jc.title as category_title
                    FROM `job_listing` jl
                    JOIN `company` c ON jl.company_id = c.id
                    LEFT JOIN `job_category` jc ON jl.job_category_id = jc.id
                    WHERE jl.`name` LIKE ? AND jl.`published` = 1 AND DATE(jl.`deadline`) >= CURDATE()
                    ORDER BY jl.`deadline` ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $filterWord);
        } else {
            // If it's not, generate placeholders for each category ID and include the IN clause in the SQL query
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $sql = "SELECT jl.*, c.name as company_name, jc.title as category_title
                    FROM `job_listing` jl
                    JOIN `company` c ON jl.company_id = c.id
                    LEFT JOIN `job_category` jc ON jl.job_category_id = jc.id
                    WHERE jl.`name` LIKE ? AND jl.`job_category_id` IN ($placeholders) AND jl.`published` = 1 AND DATE(jl.`deadline`) >= CURDATE()
                    ORDER BY jl.`deadline` ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(str_repeat('s', count($categoryIds) + 1), $filterWord, ...$categoryIds);
        }

        if($stmt->execute()) {
            $result = $stmt->get_result();
            $listings = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $listings;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Function to get all the categories
     * @return array|false with all the categories or false if something goes wrong
     */
    function getAllCategories() {
        $sql = 'SELECT * FROM `job_category` ORDER BY `title` ASC';
        $stmt = $this->conn->prepare($sql);

        // If the statement executes, return an array with all the available categories. Else return false.
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $categories = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $categories;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Function to get all the information from a listing
     * @param int The id of the listing
     * @return array|false with all the information from a listing or false if something goes wrong
     */
    function getListing($listingId) {
        // Select all listing info, from both the listing, company and job_category tables
        $sql = 'SELECT l.id, l.name, l.description, l.deadline, l.published, l.views, l.company_id, l.job_category_id, jc.title as job_category_name, c.name as company_name, c.description as company_description
            FROM job_listing l
            JOIN company c ON l.company_id = c.id
            LEFT JOIN job_category jc ON l.job_category_id = jc.id
            WHERE l.id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

        // Do a quick if, just in case something fails while executing the statement
        if ($stmt->execute()) {
            $stmt->store_result();
    
            if ($stmt->num_rows == 1) {
                // If one row is found, everything issa okay. Store the various data in variables.
                $stmt->bind_result($id, $name, $description, $deadline, $published, $views, $companyId, $jobCategoryId, $jobCategoryTitle, $companyName, $companyDescription);
                $stmt->fetch();
                $stmt->close();

                // Return an array with all the listing information, ready to be processed however you want.
                return array(
                    'id' => $id,
                    'name' => $name,
                    'description' => $description,
                    'deadline' => $deadline, 
                    'published' => $published,
                    'views' => $views,
                    'companyId' => $companyId,
                    'companyName' => $companyName,
                    'companyDescription' => $companyDescription,
                    'jobCategoryId' => $jobCategoryId,
                    'jobCategoryTitle' => $jobCategoryTitle,
                );
            } else {
                // Return false if not found 1 row exactly. 0 rows = listing doesn't exist. More than 1 row = something's very wrong in the DB.
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
     * Function to see if a listing is published
     * @param int Id of the listing to be checked
     * @return bool true if listing is published, false if not
     */
    function isListingPublished($listingId) {
        $sql = 'SELECT `published` FROM `job_listing` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($published);
                $stmt->fetch();
                $stmt->close();
                return $published;
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
     * Function to see if a listing deadline has not passed
     * @param int Id of the listing to be checked
     * @return bool true if listing deadline has not passed, false if it has
     */
    function isListingDeadlineNotPassed($listingId) {
        $sql = 'SELECT `deadline` FROM `job_listing` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($deadline);
                $stmt->fetch();
                $stmt->close();
                if ($deadline == NULL) {
                    return true;
                } else {
                    $deadline = strtotime($deadline);
                    $today = strtotime(date('Y-m-d'));
                    if ($deadline >= $today) {
                        return true;
                    } else {
                        return false;
                    }
                }
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
     * Creates an empty listing
     * @param int The id of the company
     * @return int|false with the id of the new listing or false if something goes wrong
     */
    function createNewListing($companyId) {
        // Create a new empty listing
        $sql = 'INSERT INTO `job_listing` (`company_id`) VALUES (?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);

        // If the statement executes, return the listing id. Else return false.
        if ($stmt->execute()) {
            $listingId = $this->conn->insert_id;
            $stmt->close();
            return $listingId;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Updates a listing
     * @param int $listingId The id of the listing
     * @param string $name The name of the listing
     * @param string $description The description of the listing
     * @param string $deadline The deadline of the listing
     * @param int $jobCategoryId (optional) The id of the category of the listing
     * @return bool true if successful, false if not
     */
    function updateListing($listingId, $name, $description, $deadline, $jobCategoryId=NULL) {
        $sql = 'UPDATE `job_listing` SET `name` = ?, `description` = ?, `deadline` = ?, `job_category_id` = ? WHERE `job_listing`.`id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssii', $name, $description, $deadline, $jobCategoryId, $listingId);

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
     * Deletes a listing
     * @param int The id of the listing
     * @return bool true if successful, false if not
     */
    function deleteListing($listingId) {
        $sql = 'DELETE FROM `job_listing` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

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
     * Toggles the visibility of a listing
     * @param int The id of the listing
     * @return bool true if successful, false if not
     */
    function toggleListingPublished($listingId) {
        $sql = '
            UPDATE `job_listing`
            SET published = CASE
                WHEN published = 0 THEN 1
                WHEN published = 1 THEN 0
            END
            WHERE `id` = ?;
            ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

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
     * Adds a view to a listing
     * @param int The id of the listing
     * @return bool true if successful, false if not
     */
    function addListingView($listingId) {
        $sql = 'UPDATE `job_listing` SET `views` = `views` + 1 WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $listingId);

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
     * Sets the category of a listing
     * @param int $categoryId The id of the category
     * @param int $listingId The id of the listing
     */
    function setListingCategory($categoryId, $listingId) {
        $sql = 'UPDATE `job_listing` SET `job_category_id` = ? WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $categoryId, $listingId);

        if($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }


    /**
     * Checks if a category id exist in the DB
     * @param int Id for the category in the DB
     * @return bool True if the category id exist in the DB, false if not
     */
    function checkCategoryId($categoryId) {
        $sql = 'SELECT * FROM `job_category` WHERE `id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $categoryId);

        if($stmt->execute()) {
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
}
?>