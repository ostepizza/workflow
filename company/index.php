<?php include_once '../assets/include/template.php';

function display() {
?>
<!-- Content here -->
<!-- If not a part of a company -->
<?php

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();
$dbhl = new DBHandlerListing();

// If the page is loaded with a get request, display a message depending on the request
if(!empty($_GET)){
    $getMsg = NULL;
    $getMsgColor = NULL;
    if(isset($_GET['deletedCompany'])) {
        $getMsg = 'Company has been deleted.';
        $getMsgColor = 'danger';
    } else if (isset($_GET['registerSuccess'])) {
        $getMsg = 'Company successfully registered.';
        $getMsgColor = 'success';
    }
    echo('<div class="alert alert-' . $getMsgColor . ' mt-3" role="alert">' . $getMsg . '</div>');  
}

// Check if the user is a part of a company
if ($companyId = $dbhc->getCompanyIdFromUserId($_SESSION['user_id'])){
    // If member is in a company, retrieve  and set company data
    $companyDetails = $dbhc->getCompanyDetailsFromCompanyId($companyId);

    // Set some shorthands for name and description
    $companyName = $companyDetails['companyName'];
    $companyDescription = $companyDetails['companyDescription'];

    // Retrieve all listings for company
    $listings = $dbhl->getAllCompanyListings($companyId);

    // Retrieve listing statistics for company 
    $statistics = $dbhl->getCompanyStatistics($companyId);

    // Display the company dashboard:
?>
    <div class="row mt-5">
        <div class="col-md-12">
            <?php
            echo '<h1>' . $companyName . ' - Company dashboard</h1>';
            echo '<p>' . $companyDescription . '</p>';
            ?>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-7">
            <?php
            echo '<h2>Job listings ('.count($listings).'):</h2><hr>';
            if ($listings) {
                foreach($listings as $listing) {
                    $listing['name'] = $listing['name'] ?? 'No title';
    
                    $listing['description'] = $listing['description'] ?? '<i>This listing has no description</i>';
                    if (strlen($listing["description"]) > 250) {
                        $listing['description'] = substr($listing['description'], 0, 250) . '...';
                    }
    
                    if ($listing['deadline'] == NULL) {
                        $listing['deadline'] = 'No deadline';
                    } else {
                        $listing['deadline'] = 'Apply before ' . date('d. M Y', strtotime($listing['deadline']));
                    }
    
                    $listing['published'] = $listing['published'] == 1 ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-danger">Unpublished</span>';
    
                    $listing['category_title'] = $listing['category_title'] ?? 'No category';
    
                    echo '
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-9">
                                    <b>' . $listing['name'] . '</b> ('.$listing['views'].' views)
                                </div>
                                <div class="col-md-3">
                                    <p class="text-end mb-0">' . $listing['published'] . '</p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <p>'.$listing['description'].'</p>
                                    <hr>
                                    ' . $listing['deadline'] . '<br>
                                    ' . $listing['category_title'] . '
                                </div>
                                <div class="col-md-3">
                                    <a href="#"><button type="button" class="btn btn-primary w-100">View applications</button></a>
                                    <a href="../jobs/edit.php?id='.$listing['id'].'"><button type="button" class="btn btn-secondary w-100 mt-2">Edit listing</button></a>
                                    <a href="../jobs/listing.php?id='.$listing['id'].'"><button type="button" class="btn btn-secondary w-100 mt-2">View listing</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo '<p>There are no job listings.</p>';
            }
            
            ?>
        </div>

        <div class="col-md-1">
        </div>

        <div class="col-md-3">
            <div class="row">
                <h2>Manage</h2>
                <hr>
                <a href="../jobs/new.php"><button type="button" class="btn btn-primary w-100">Create new job listing</button></a>
                <a href="view_members.php"><button type="button" class="btn btn-secondary w-100 mt-2">View members of company</button></a>
                <a href="edit.php"><button type="button" class="btn btn-secondary w-100 mt-2">Edit company details</button></a>
            </div>
            <div class="row mt-3">
                <h2>Statistics</h2>
                <hr>
                <p>
                    <?php
                    echo 'Published listings: ' . $statistics['published_listings'] . '<br>';
                    echo 'Unpublished listings: ' . $statistics['unpublished_listings'] . '<br>';
                    echo 'Total views: ' . $statistics['total_views'] . '<br>';
                    ?>
                </p>
            </div>
        </div>
    </div>
<?php
} else {
    // If not part of a company, show an alternate dashboard
    echo '
    <div class="row mt-5">
        <div class="col-md-12">
            <h1>Company dashboard</h1>
            <p>This section is used for companies intending to use this platform for creating job applications and finding new employees.</p>
            <p><b>You are currently not a member of a company. <a href="new.php">Create one</a>, or wait to be added to one.</b></p>
        </div>
    </div>
    ';
}
?>
<!-- Content here -->
<?php
}

makePage('display', 'Company dashboard', requireLogin: true);