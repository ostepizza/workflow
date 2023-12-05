<?php
class DBHandlerStatistics extends DBHandlerBase {
    function getSystemTotalViews() {
        $sql = 'SELECT SUM(views) as total_views FROM `job_listing`';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalViews = $result->fetch_assoc()['total_views'];
        $stmt->close();
        return $totalViews;
    }

    function getSystemPublishedListingsCount() {
        $sql = 'SELECT COUNT(*) as published_listings FROM `job_listing` WHERE `published` = 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $publishedListings = $result->fetch_assoc()['published_listings'];
        $stmt->close();
        return $publishedListings;
    }

    function getSystemTotalApplicationsSent() {
        $sql = 'SELECT COUNT(*) as total_applications FROM `job_application`';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalApplications = $result->fetch_assoc()['total_applications'];
        $stmt->close();
        return $totalApplications;
    }

    function getSystemTotalCompanies() {
        $sql = 'SELECT COUNT(*) as total_companies FROM `company`';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalCompanies = $result->fetch_assoc()['total_companies'];
        $stmt->close();
        return $totalCompanies;
    }

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
    
    // Returns company statistics in an array, like number of listings, number of views, etc. Returns false if something goes wrong.
    function getCompanyStatistics($companyId) {
        return [
            'total_views' => $this->getCompanyTotalViews($companyId),
            'published_listings' => $this->getCompanyPublishedListingsCount($companyId),
            'unpublished_listings' => $this->getCompanyUnpublishedListingsCount($companyId),
            'received_applications' => $this->getCompanyTotalApplicationsReceived($companyId)
        ];
    }

    function getUserTotalApplicationsSent($userId) {
        $sql = 'SELECT COUNT(*) as total_applications FROM `job_application` WHERE `user_id` = ?';
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