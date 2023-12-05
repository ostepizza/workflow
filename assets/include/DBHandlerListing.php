<?php
class DBHandlerListing extends DBHandlerBase {
    // Retrieves an array of published listings where the deadline hasn't passed yet. Returns false if something goes wrong.
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

    // Retrieves all listings from a company id. Returns false if there are no listings or something goes wrong.
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

    // Searches for listings matching a filter word and an array of category IDs. Returns false if something goes wrong.
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

    

    // Retrieves an array of available job categories. Else returns false.
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

    // Retrieves all listing information from a listing id. Returns an array with the listing info if successful, else returns false.
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

    // Checks if a listing is published. Returns true if published, else returns false.
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

    // Creates a new listing and returns the new listing ID if successful. Else returns false.
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

    // Updates a listing. Returns true if successful, else returns false.
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

    // Deletes a listing. Returns true if successful, else returns false.
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

    // Toggles the visibility of a listing. Returns true if successful, else returns false. 0 = only visible to company, 1 = visible to all
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

    // Add a view to a listing. Returns true if successful, else returns false.
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

    // Sets the category of a listing. Returns true if successful, else returns false.
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