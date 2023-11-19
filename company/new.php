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

// Ask database if logged in member is found in the company_management table
if ($dbhc->getCompanyIdFromUserId($_SESSION['user_id'])){
    // Redirect if user is already in a company
    header('Location: ../403.php');
}

// If user has requested to register a new company
if (isset($_POST['submit'])) {
    // Validate company name and description
    $validator->validateCompanyName($_POST['name']);
    $validator->validateCompanyDescription($_POST['description']);

    // If form input is valid, check if the company name is taken
    if ($validator->valid) {
        // If the company name is not taken, try to craete a company
        if(!$dbhc->isCompanyNameTaken($_POST['name'])) {
            
            if($dbhc->createNewCompany($_POST['name'], $_POST['description'], $_SESSION['user_id'])) {
                /* 
                    Try to create a company with the name and description provided in the form
                    If the company is created successfully, the user is automatically added to the company.
                    Redirect with a success-message.
                */
                header('Location: index.php?registerSuccess');
            } else {
                // Statement didn't execute properly
                $feedbackForUser = "An error occurred while creating a company.<br>";
            }
        } else {
            // If the company name is taken
            $feedbackForUser = "Company with this name already exists!<br>";
        }
    } else {
        // If the form validation failed, tell the user what went wrong.
        $feedbackForUser = $validator->printAllFeedback();
    }
}

function display() {
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <a href="index.php"><button type="button" class="btn btn-secondary mb-3">&lt; Return to dashboard</button></a>
        <h1>New company</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Company name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your company name" autofocus>
            </div>

            <div class="form-group mt-3">
                <label for="description">Company description (optional, max 500 characters)</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Write a quick description about what your company does"></textarea>
            </div>

            <button type="submit" id="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'New company', $feedbackForUser, $feedbackColor, requireLogin: true);