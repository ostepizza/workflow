<?php
class DBHandlerStatistics extends DBHandlerBase {
    /** 
     * Gets the total view count for all job listings in the system
     * @return int the number of views for all job listings in system
    */
    function getSystemTotalViews() {
        $sql = 'SELECT SUM(views) as total_views FROM `job_listing`';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalViews = $result->fetch_assoc()['total_views'];
        $stmt->close();
        return $totalViews;
    }

    /**
     * Get the total number of all published job listings in the system
     * @return int number of all published job listings in system
     */
    function getSystemPublishedListingsCount() {
        $sql = 'SELECT COUNT(*) as published_listings FROM `job_listing` WHERE `published` = 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $publishedListings = $result->fetch_assoc()['published_listings'];
        $stmt->close();
        return $publishedListings;
    }


    /**
     * Get the total number of all applications sent in the system
     * @return int number of applications in system 
     */
    function getSystemTotalApplicationsSent() {
        $sql = 'SELECT COUNT(*) as total_applications FROM `job_application`';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalApplications = $result->fetch_assoc()['total_applications'];
        $stmt->close();
        return $totalApplications;
    }


    /**
     * Get the amount of companies registered in the system
     * @return int returns the number of registered companies
     */
    function getSystemTotalCompanies() {
        $sql = 'SELECT COUNT(*) as total_companies FROM `company`';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalCompanies = $result->fetch_assoc()['total_companies'];
        $stmt->close();
        return $totalCompanies;
    }

    /**
     * Get the total amount of listing views of a company
     * @param int the id of the company
     * @return int number of listing views
     */
    function getCompanyTotalViews($companyId) {
        $sql = 'SELECT SUM(views) as total_views FROM `job_listing` WHERE `company_id` = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalViews = $result->fetch_assoc()['total_views'];
        $stmt->close();
        return $totalViews;
    }
    
    /**
     * Get a number of published listing a company has
     * @param int the id of the company
     * @return int number of published listings for a company
     */
    function getCompanyPublishedListingsCount($companyId) {
        $sql = 'SELECT COUNT(*) as published_listings FROM `job_listing` WHERE `company_id` = ? AND `published` = 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $publishedListings = $result->fetch_assoc()['published_listings'];
        $stmt->close();
        return $publishedListings;
    }
    
    /**
     * Get the amount of unpublished listings for a company
     * @param int id for the company
     * @return int Number of all unpublished listings to the company
     */
    function getCompanyUnpublishedListingsCount($companyId) {
        $sql = 'SELECT COUNT(*) as unpublished_listings FROM `job_listing` WHERE `company_id` = ? AND `published` = 0';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $unpublishedListings = $result->fetch_assoc()['unpublished_listings'];
        $stmt->close();
        return $unpublishedListings;
    }

    /**
     * Gets the total amount of applications
     * @param int The id for the company
     * @return int A number of applications per company
     */
    function getCompanyTotalApplicationsReceived($companyId) {
        $sql = 'SELECT COUNT(*) as received_applications 
                FROM `job_application` as ja 
                JOIN `job_listing` as jl ON ja.`job_listing_id` = jl.`id`
                WHERE jl.`company_id` = ? AND `sent` = 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $receivedApplications = $result->fetch_assoc()['received_applications'];
        $stmt->close();
        return $receivedApplications;
    }
    
    /**
     * Get the statistics for a company
     * @param int The id of the company
     * @return array An array of statistics for a company
     */
    function getCompanyStatistics($companyId) {
        return [
            'total_views' => $this->getCompanyTotalViews($companyId),
            'published_listings' => $this->getCompanyPublishedListingsCount($companyId),
            'unpublished_listings' => $this->getCompanyUnpublishedListingsCount($companyId),
            'received_applications' => $this->getCompanyTotalApplicationsReceived($companyId)
        ];
    }

    /**
     * Get the amount applications per user
     * @param int The id of the user
     * @return int Gets a number of applications a user has sent
     */
    function getUserTotalApplicationsSent($userId) {
        $sql = 'SELECT COUNT(*) as total_applications FROM `job_application` WHERE `user_id` = ? AND `sent` = 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalApplications = $result->fetch_assoc()['total_applications'];
        $stmt->close();
        return $totalApplications;
    }
}
?>