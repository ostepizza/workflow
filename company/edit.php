<?php include_once '../assets/include/template.php';

$feedbackForUser = NULL;
$feedbackColor = "danger";

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();
$dbhc = new DBHandlerCompany();

// If the user is a company superuser
if ($dbhc->isUserCompanySuperuser($_SESSION['user_id'])) {
    // If member is a superuser of the company, retrieve company data
    $companyId = $dbhc->getCompanyIdFromUserId($_SESSION['user_id']);
    $companyDetails = $dbhc->getCompanyDetailsFromCompanyId($companyId);
    $companyName = $companyDetails['companyName'];
    $companyDescription = $companyDetails['companyDescription'];
} else {
    // Redirect if user isn't a company superuser
    header('Location: ../403.php');
}

//Handle the two forms available on the page
if (isset($_POST['companyUpdate'])) {
    $validator->validateCompanyName($_POST['name']);
    $validator->validateCompanyDescription($_POST['description']);

    if ($validator->valid) {
        if(!$dbhc->isCompanyNameTaken($_POST['name'], $companyId)) {
            // This next section seems inefficient but I'm tired
            $dbhc->updateCompanyNameWithCompanyId($companyId, $_POST['name']);
            $dbhc->updateCompanyDescriptionWithCompanyId($companyId, $_POST['description']);
            $feedbackForUser .= 'Company details were updated.<br>';
            $feedbackColor = 'success';   
            $companyDetails = $dbhc->getCompanyDetailsFromCompanyId($companyId);
            $companyName = $companyDetails['companyName'];
            $companyDescription = $companyDetails['companyDescription'];
        } else {
            $feedbackForUser .= "Company with this name already exists!<br>";
        }
        
    } else {
        // If the form validation failed, tell the user what went wrong.
        $feedbackForUser = $validator->printAllFeedback();
    }

} else if (isset($_POST['companyDelete'])) {
    //At this point, the user has both been authenticated as being a member of the company, and a superuser for the company.
    //We can therefore continue with the deletion attempt with no further questioning regarding company membership.

    $continueDeletion = true;
    $feedbackForUser;

    //Check both checkmark and password field
    //Don't attempt to continue deletion if either isn't filled
    if(!isset($_POST['deleteCheckmark'])){
        $continueDeletion = false;
        $feedbackForUser .= "You need to check the checkbox in order to initiate company deletion.<br>";
    }

    if(empty($_POST['currentPassword'])){
        $continueDeletion = false;
        $feedbackForUser .= "You need to confirm your password in order to initiate company deletion.<br>";
    }

    //If password field isn't empty and checkmark is checked, we can start doing some further checks to see if all criterias are met
    //If all criterias are met, we delete the company
    if($continueDeletion == true) {

        //Retrieve users hashed password from the database for comparison
        if($hashedPassword = $dbhu->getUserPasswordByUserId($_SESSION['user_id'])) {
            // compare the password entered and the hashed password from the database
            if($dbhu->verifyPassword($_POST['currentPassword'], $hashedPassword)) {
                echo 'the passwords are the same';
                if ($dbhc->deleteCompanyById($companyId)) {
                    //Redirect if successfully deleted
                    header('Location: index.php?deletedCompany');
                } else {
                    $feedbackForUser .= "An error occurred while attempting to delete the company.<br>";
                }
            } else {
                $feedbackForUser .= "Wrong password input. Company is not deleted.<br>";
            }
        } else {
            $feedbackForUser .= "An error occurred while retrieving user data.<br>";
        }
    }
}

function display() {
global $companyName, $companyDescription;
?>

<!-- Content here -->
<a href="index.php"><button type="button" class="btn btn-secondary mb-3 mt-5">&lt; Return to dashboard</button></a>

<div class="row">
    <div class="col-md-8">
        <h1>Edit company details <a href="#" data-bs-toggle="modal" data-bs-target=".modalDeleteCompany"><button type="button" class="btn btn-danger">Delete company</button></a></h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Company name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo $companyName; ?>" autofocus>
            </div>

            <div class="form-group mt-3">
                <label for="description">Company description (optional, max 500 characters)</label>
                <textarea class="form-control" id="description" name="description" rows="10"><?php echo $companyDescription; ?></textarea>
            </div>

            <button type="submit" id="submit" name="companyUpdate" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>
</div>

<div class="modal fade modalDeleteCompany" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete company</h5>
            </div>
            <div class="modal-body">
                <p><span class="text-danger"><b>WARNING! This is a destructive action!</b></span></p>
                <p>By proceeding, the company "<?php echo $companyName; ?>" will be deleted. With this action, all related job listings and user submitted job applications will also be deleted. This action <b>CAN NOT BE REVERSED.</b></p>
                <br>
                <p><b>If you still wish to proceed, please check the checkbox below and input your current password:</b></p>
            </div>
            <div class="modal-footer">
                <form action="" method="post" class="row row-cols-lg-auto align-items-center">
                    <div class="col-12">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="deleteCheckmark">
                        <label class="form-check-label" for="inlineFormCheck">
                            I confirm that I wish to delete the company
                        </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <input type="password" class="form-control" name="currentPassword" placeholder="Current password">
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-danger" name="companyDelete">Delete</button>
                    </div>
                </form>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
  </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Edit company', $feedbackForUser, $feedbackColor, requireLogin: true);