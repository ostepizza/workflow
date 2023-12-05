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

// Retrieve a listing and all categories from the database, based on the id in the GET request
$listingId = intval($_GET["id"]);
$listing = $dbhl->getListing($listingId);
$categories = $dbhl->getAllCategories();

// Array to store the failed listing input (currently not in use). Used to repopulate the form with the failed input
$listingFailed = array();

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

// Check first if the listing should be deleted
if (isset($_POST['deleteListing'])) {
    if ($dbhl->deleteListing($listingId)) {
        header('Location: ../company/index.php?deletedListing');
        exit();
    } else {
        $feedbackForUser = 'Failed to delete listing.';
        $feedbackColor = 'danger';
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Else, if any other post request is sent, validate the input and update the listing
    $validator->validateJobListingTitle($_POST['title']);
    $validator->validateJobListingDescription($_POST['description']);
    $validator->validateJobListingDeadline($_POST['deadline']);

    if($validator->valid) {
        // Check if selected category exists
        if ($dbhl->checkCategoryId($_POST['category'])) {
            $categoryId = $_POST['category'];
        } else {
            $categoryId = NULL;
        }

        // If the form validation succeeded, update the listing
        if ($dbhl->updateListing($listingId, $_POST['title'], $_POST['description'], $_POST['deadline'], $categoryId)) {
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
        $feedbackColor = 'danger';
    }
}

// Debugging
//if (!empty($listingFailed)) {
//    print_r($listingFailed);
//}

function display() {
global $listing, $categories;

?>
<!-- Content here -->
<a href="../company/index.php"><button type="button" class="btn btn-secondary mb-3 mt-5"><i class="fa-solid fa-circle-left"></i> Return to dashboard</button></a>

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
                    <textarea class="form-control" name="description" id="description" rows="12" placeholder="Please enter a description"><?php if(!empty($listing['description'])) { echo $listing['description'];} ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="form-group col-md-6">
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
                        <div class="form-group col-md-6">
                            <label for="category">Category</label><br>
                            <select id="category" name="category">
                                <option value="">-None-</option>
                                <?php
                                foreach ($categories as $category) {
                                    $selected = ($listing['jobCategoryId'] == $category['id']) ? 'selected' : '';
                                    echo '<option value="' . $category['id'] . '"' . $selected . '>' . $category['title'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                </div>

                <button type="submit" id="submitSave" name="submitSave" class="btn btn-primary mt-3 mb-3">Save listing</button>
                <?php 
                    if ($listing['published'] == 1) {
                        echo '<button type="submit" id="submitPublish" name="submitPublish" class="btn btn-warning mt-3 mb-3">Save and unpublish listing</button>';
                    } else {
                        echo '<button type="submit" id="submitPublish" name="submitPublish" class="btn btn-success mt-3 mb-3">Save and publish listing</button>';
                    }
                ?>
                <a href="#" data-bs-toggle="modal" data-bs-target=".modalDeleteListing"><button type="button" class="btn btn-danger">Delete listing</button></a>
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
                <b>Category</b><br>
                The category of the job listing. This will be used to sort and categorize the job listing.<br>
            </p>
        </div>
    
</div>

<div class="modal fade modalDeleteListing" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete listing</h5>
            </div>
            <div class="modal-body">
                <p><span class="text-danger"><b>WARNING! This is a destructive action!</b></span></p>
                <p>By proceeding, the current listing will be deleted. With this action, all user submitted job applications related to this listing will also be deleted. This action <b>CAN NOT BE REVERSED.</b></p>
            </div>
            <div class="modal-footer">
                <form action="" method="post" class="row row-cols-lg-auto align-items-center">
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger" name="deleteListing">Delete</button>
                    </div>
                </form>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?php

}

makePage('display', 'Edit job listing', $feedbackForUser, $feedbackColor, requireLogin: true);
