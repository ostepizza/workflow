<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();
$dbhl = new DBHandlerListing();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Default feedback for user
$feedbackForUser = NULL;
$feedbackColor = "danger";

// Retrieve a listing from the database, based on the id in the GET request
$listingId = intval($_GET["id"]);
$listing = $dbhl->getListing($listingId);

// Redirect the user if they are not a part of the company that owns the listing
if($dbhc->getCompanyIdFromUserId($_SESSION['user_id']) != $listing['companyId']) {
    header('Location: ../403.php');
    exit();
}

// Some GET-messages to show user feedback after listing updates
if (isset($_GET['savedListing'])) {
    $feedbackForUser .= 'Successfully updated listing.<br>';
    $feedbackColor = 'success';
}
if (isset($_GET['toggledVisibility'])) {
    $feedbackForUser .= 'Successfully changed listing visibility.<br>';
    $feedbackColor = 'success';
}

// If a post request is sent, validate the input and update the listing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the job listing input
    $validator->validateJobListingTitle($_POST['title']);
    $validator->validateJobListingDescription($_POST['description']);
    $validator->validateJobListingDeadline($_POST['deadline']);

    if($validator->valid) {
        // If the form validation succeeded, update the listing

        if ($dbhl->updateListing($listingId, $_POST['title'], $_POST['description'], $_POST['deadline'])) {
            // If the listing was successfully updated, tell the user. This is only needed IF it fails to publish
            $feedbackForUser = 'Successfully updated listing.<br>';
            $feedbackColor = 'success';

            // Proceed to toggle the listing visibility (published) if the user clicked the 'Save and publish listing' button
            if (isset($_POST['submitPublish'])) {
                if ($dbhl->toggleListingPublished($listingId)) {
                    // Refresh the page with a get message to show that the listing was saved and published
                    header('Location: edit.php?id=' . $listingId . '&savedListing&toggledVisibility');
                    exit();
                } else {
                    $feedbackForUser = 'Failed to publish listing.';
                }
            }

            // Refresh the page with a get message to show that the listing was saved
            header('Location: edit.php?id=' . $listingId . '&savedListing');
        } else {
            $feedbackForUser = 'Failed to update listing.';
        }

    } else {
        // If the form validation failed, tell the user what went wrong.
        $feedbackForUser = $validator->printAllFeedback();
    }
}

function display() {
global $listing;

?>
<!-- Content here -->
<a href="../company/index.php"><button type="button" class="btn btn-secondary mb-3 mt-5">&lt; Return to dashboard</button></a>

<div class="row">
    <p>
        <span class="h1">Edit job listing</span>
        <?php
            if ($listing['published'] == 1) {
                echo '<span class="badge bg-success h5">Currently published</span>';
            } else {
                echo '<span class="badge bg-danger h5">Currently unpublished</span>';
            }
        ?>
    </p>
    
        <div class="col-md-8">
            <form action="" method="post">
                <div class="form-group mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Please enter a title" <?php if(!empty($listing['name'])) { echo 'value="' . $listing['name'] . '"';} ?>>
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3" placeholder="Please enter a description"><?php if(!empty($listing['description'])) { echo $listing['description'];} ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="deadline">Deadline to apply</label><br>
                    <input type="date" id="deadline" name="deadline" 
                    <?php
                        if(!empty($listing['deadline'])){
                            echo 'value="' . $listing['deadline'] . '"';
                        } else {
                            echo 'value="' . date("Y-m-d") . '"';
                        } 
                    ?>
                    /><br>
                </div>

                <button type="submit" id="submitSave" name="submitSave" class="btn btn-primary mt-3 mb-3">Save listing</button>
                <?php 
                    if ($listing['published'] == 1) {
                        echo '<button type="submit" id="submitPublish" name="submitPublish" class="btn btn-danger mt-3 mb-3">Save and unpublish listing</button>';
                    } else {
                        echo '<button type="submit" id="submitPublish" name="submitPublish" class="btn btn-success mt-3 mb-3">Save and publish listing</button>';
                    }
                ?>
            </form>
        </div>

        <div class="col-md-1"></div>

        <div class="col-md-3">
            <h2>Guide</h2>
            <p>
                <b>Title</b><br>
                The title of the job listing. This will be the first thing the user sees, so make it count!<br>
                <br>
                <b>Description</b><br>
                The description of the job listing. This should contain information about the job, the company and the requirements.<br>
                <br>
                <b>Deadline</b><br>
                The deadline for the job listing. This will be the last day the user can apply for the job.<br>
                <br>
            </p>
        </div>
    
</div>
<?php

}

makePage('display', 'Edit job listing', $feedbackForUser, $feedbackColor, requireLogin: true);
