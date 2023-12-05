<?php include_once '../assets/include/template.php';
/* 
    Edit a job application, which is only accessible to the user who created the application.
    It is accessed through a get request for the application id. 
    Necessary checks are be made to ensure that the user is the owner of the application.
    If the deadline for the listing has passed, the user is not able to edit the application and is redirected.
*/
include_once '../assets/include/Validator.php';
$validator = new Validator();

include_once '../assets/include/DBHandler.php';
$dbha = new DBHandlerApplication();
$dbhc = new DBHandlerCompany();

//Set up emty variables for feedback
$feedbackForUser = NULL;
$feedbackColor = "danger";

// If the application has been updated
if(isset($_GET["updatedApplication"])) {
    $feedbackForUser .= 'Job application has been updated.<br>';
    $feedbackColor = 'success';
}

// Retrieve the application id from the get request
$applicationId = $_GET["id"];

// Retrieve application
if ($application = $dbha->getApplication($applicationId)) {
    // If an application is found, check the ownership

    if ($application['user_id'] != $_SESSION['user_id'] || $application['sent'] == 1) {
        // If the user is not the owner of the application, redirect to 403
        header('Location: ../403.php');
        exit();
    } 

} else {
    // If no application is found, redirect to 403 to not give away information about the existence of any applications
    header('Location: ../403.php');
    exit();
}

// If either of the buttons are pressed, update the application
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['applicationDelete'])) {
        // If the user has requested to delete the application, delete it and redirect to the user overview
        if ($dbha->deleteApplication($applicationId)) {
            header("Location: ../user/index.php?deletedApplication");
            exit();
        } else {
            $feedbackForUser = 'An error occurred while deleting the application.';
            $feedbackColor = 'danger';
        }
    }

    $title = strip_tags($_POST['title']);
    $description = strip_tags($_POST['description']);

    $validator->validateJobApplicationTitle($title);
    $validator->validateJobApplicationDescription($description);
    
    if ($validator->valid) {
        //If the validation is successful, try to update the application

        if($dbha->updateApplicationContent($applicationId, $title, $description)){
            //After updating the application, send it if the user has requested to do so.

            if (isset($_POST['applicationSend'])) {
                // If the user has requested to send the application, send it and redirect to the view page
                if ($dbha->sendApplication($applicationId)) {
                    header("Location: view.php?id=$applicationId&sentApplication");
                    exit();
                } else {
                    $feedbackForUser = 'An error occurred while sending the application.';
                    $feedbackColor = 'danger';
                }
            }

            // Refresh the page to show the updated application
            header("Location: edit.php?id=$applicationId&updatedApplication");
            exit();

        } else {
            $feedbackForUser = 'An error occurred while updating the application.';
            $feedbackColor = 'danger';
        }
    } else {
        $feedbackForUser = $validator->printAllFeedback();
        $feedbackColor = 'danger';
    }
}

// In case company description is missing, set it to a default value
$application['company_description'] = (isset($application['company_description']) && $application['company_description'] !== '') ? $application['company_description'] : 'No description';

function display() {
global $application;
?>

    <a href="../user/index.php" class="btn btn-secondary mt-3" role="button">Go to application overview</a><br>

    <div class="row mt-3">
        <div class="col-md-12">
            <span class="h1">Application to <?php echo $application['company_name']?></span> <span class="badge bg-warning">Draft</span><br>
            <span><i>Regarding listing <?php echo $application['listing_name']?></i></span>
        </div>
    </div>

    <form action="" method="post">
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="name">Application Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Please enter a title" <?php if(!empty($application['title'])) { echo 'value="' . $application['title'] . '"';} ?>>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="description">Application Description</label>
                    <textarea class="form-control" name="description" rows="16" placeholder="Describe why you're the best for the job..."><?php if(!empty($application['text'])) { echo $application['text'];} ?></textarea>
                </div>
            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header text-center">
                        About employer
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $application['company_name'];?></h5>
                        <p>
                            <?php
                            echo $application['company_description'];
                            ?>
                        </p>
                        <hr>
                        <p>
                            By sending this application, you agree to share <a href="#" data-bs-toggle="modal" data-bs-target=".modalInfoToShare">the following information</a> with the company.
                        </p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-success w-100 mb-2" type="submit" name="applicationUpdate">Save</button>
                                <a href="#" data-bs-toggle="modal" data-bs-target=".modalDeleteApplication" class="btn btn-danger w-100 mb-2">Delete</a>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-warning w-100" type="submit" name="applicationSend">Save & send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade modalInfoToShare" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Info that will be shared with company <?php echo $application['company_name']?></h5>
                    </div>
                    <div class="modal-body">
                        <p>
                            By sending this application, you agree to share the following information with the company:<br>
                            - Your full name<br>
                            - Your email address<br>
                            - Your phone number (if set)<br>
                            - Your location (if set)<br>
                            - Your profile picture (if uploaded)<br>
                            - Your CV (if uploaded)<br>
                            - Your competence<br>
                            - Your application title & text<br>
                            <br>
                            Any information you may update regarding your profile, will be updated for the company as well.<br>
                            <br>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Dismiss</button>
                    </div>
                </div>
        </div>
    </div>

    <div class="modal fade modalDeleteApplication" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete application</h5>
                    </div>
                    <div class="modal-body">
                        <p><span class="text-danger"><b>WARNING! This is a destructive action!</b></span></p>
                        <p>By proceeding, the current application will be deleted. This action <b>CAN NOT BE REVERSED.</b></p>
                    </div>
                    <div class="modal-footer">
                        <form action="" method="post" class="row row-cols-lg-auto align-items-center">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger" name="applicationDelete">Delete</button>
                            </div>
                        </form>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
        </div>
    </div>

<?php
}

makePage('display', 'Job listings', $feedbackForUser, $feedbackColor, requireLogin: true);