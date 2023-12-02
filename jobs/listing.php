<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();
$dbhl = new DBHandlerListing();

$listingId = intval($_GET["id"]);
$listing = $dbhl->getListing($listingId);

if(($listing['published'] == 0) && ($dbhc->getCompanyIdFromUserId($_SESSION['user_id']) != $listing['companyId'])) {
    // Redirect if listing isn't published, and user is not a part of the company that owns the listing
    header('Location: ../403.php');
    exit();
} else if (($listing['published'] == 1)) {
    // If the listing is published, add a view to the listing
    $dbhl->addListingView($listingId);
}

function display() {
global $listing;
?>
    <div class="row mt-4">
        <!-- Headline of the job listing -->
        <div class="col-md-8">
        <a class="btn btn-secondary" href="index.php">Go back to job listings</a>
        </div>
    </div>
    <?php
    //Checks to see if content is NULL. If it is, placeholders for job name, description, deadline, etc
    $listing['name'] = $listing['name'] ?? 'Missing job title';
    $listing['description'] = $listing["description"] ?? "No information about current job listing";

    $listing["companyName"] = $listing["companyName"] ?? "Missing company name";
    $listing["companyDescription"] = $listing["companyDescription"] ?? "Missing company description";
    
    if ($listing["jobCategoryTitle"] == NULL) {
        $listing["jobCategoryTitle"] = 'No category';
    } else {
        $listing["jobCategoryTitle"] = 'Category: ' . $listing['jobCategoryTitle'];
    }

    if ($listing['deadline'] == NULL) {
        $listing['deadline'] = 'No deadline';
    } else {
        $listing['deadline'] = 'Apply before ' . date('d. M Y', strtotime($listing['deadline']));
    }
    
    echo'
        <div class="row mt-5">
        <div class="col-md-7">
            <h1>' . $listing["name"] . '</h1>
            <hr>
            <p>' . nl2br($listing["description"]) . '</p>
        </div>
        <!-- Whitespace begin -->
        <div class="col-md-1">
        </div>
        <!-- Whitespace end, Card section begin -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header text-center">
                    About employer
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">'. $listing["companyName"] .'</h5>
                    <p>' . $listing["companyDescription"] . '</p>
                    <hr>
                    <b class="card-text">' . $listing["deadline"] . '</b>
                    <p class="card-text">' . $listing["jobCategoryTitle"] . '</p>
                    <hr>

                    <div class="col-md-4 mx-auto">
                        <a class="btn btn-primary" href="application.php?companyId=' . $listing['companyId'] . '">Apply for job</a>
                    </div>
                </div>
            </div>
        </div>
    </div>';
        
    ?>
<?php

}
makePage('display', 'Job listing');