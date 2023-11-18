<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbh = new DBHandlerUser();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Set default feedback variables
$feedbackForUser = NULL;
$feedbackColor = "danger";

if (isset($_POST['submit'])) {
    // Give ToS-checkmark a value even if user hasn't specified. Else the post-value is missing and validation won't work
    $tos = (isset($_POST['acceptToS'])) ? $_POST['acceptToS'] : false;
    
    // Pass all form elements to the Validator
    $validator->validateRegistration($tos, $_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName']);

    if ($validator->valid) {

        // If all form inputs are valid, check if email is in the system
        if(!$dbh->isEmailtaken($_POST['email'])){

            // If the email is not found in the database, proceed with registration
            if($dbh->addUserToDB($_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName'])) {
                $feedbackForUser = "User has been successfully registered. You may now log in.<br>";
                $feedbackColor = "success";
            } else {
                $feedbackForUser = "An error occurred while registering.<br>";
            }
        } else {
            // If email was found in database, display an error for the user
            $feedbackForUser = 'Email ' . $_POST['email'] . ' already belongs to a user.<br>';
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
    <div class="col-md-2"></div>

    <div class="col-md-3">
        <h1>Register</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="text" id="email" name="email" class="form-control" placeholder="Enter your email" autofocus>
                <small class="form-text text-muted">This will be used to log you in</small>
            </div>

            <div class="form-group mt-3">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your desired password">
                <small class="form-text text-muted">Must be at least 8 characters long, and contain at least 1 number and 1 special character</small>
            </div>

            <div class="form-group mt-3">
                <label for="firstName">First name</label>
                <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Enter your first name">
            </div>

            <div class="form-group mt-3">
                <label for="lastName">Last name</label>
                <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Enter your last name">
            </div>

            <div class="form-group form-check mt-3">
                <input type="checkbox" id="acceptToS" name="acceptToS" class="form-check-input">
                <label class="form-check-label" for="acceptToS">I accept the <a href="#" data-bs-toggle="modal" data-bs-target=".modalToS">terms & conditions</a></label>
            </div>

            <button type="submit" id="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
            
        </form>
    </div>

    <div class="col-md-2"></div>

    <div class="col-md-3">
        <h2>Already have an account?</h2>
        <a class="btn btn-primary" href="login.php" role="button">Log in</a>
    </div>
    
    <div class="col-md-2"></div>
</div>

<div class="modal fade modalToS" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms & Conditions</h5>
            </div>
            <div class="modal-body">
                <p>Terms and conditions for site usage.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
  </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Register', $feedbackForUser, $feedbackColor, requireNoUser: true);