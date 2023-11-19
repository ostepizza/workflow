<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

$feedbackForUser = NULL;
$feedbackColor = "danger";

// Retrieve all user info
$userInfo = $dbhu->selectAllUserInfoByUserId($_SESSION['user_id']);

// Some get messages to show user feedback after profile updates
if (isset($_GET['updatedProfile'])) {
    $feedbackForUser = 'Your profile has been updated.';
    $feedbackColor = 'success';
} else if (isset($_GET['updatedPassword'])) {
    $feedbackForUser = 'Your password has been changed.';
    $feedbackColor = 'success';
}

// If the user has requested to update their profile information
if (isset($_POST['submitProfile'])) {
    // Set color to danger in case the user has just updated something already
    $feedbackColor = 'danger';

    // Check if all the individual fields are valid
    $validator->validateEmail($_POST['email']);
    $validator->validateFirstName($_POST['firstName']);
    $validator->validateLastName($_POST['lastName']);
    $validator->validateLocation($_POST['location']);
    $validator->validateTelephone($_POST['telephone']);
    $validator->validateBirthday($_POST['birthday']);

    if($validator->valid) {
        // If all the fields are valid, check if the email is taken        

        if(!$dbhu->isEmailTaken($_POST['email'], $_SESSION['user_id'])){

            // If the email is not found in the database, proceed with registration
            $dbhu->updateEmail($_SESSION['user_id'], $_POST['email']);
            $dbhu->updateFirstName($_SESSION['user_id'], $_POST['firstName']);
            $dbhu->updateLastName($_SESSION['user_id'], $_POST['lastName']);
            $dbhu->updateLocation($_SESSION['user_id'], $_POST['location']);
            $dbhu->updateTelephone($_SESSION['user_id'], $_POST['telephone']);
            $dbhu->updateBirthday($_SESSION['user_id'], $_POST['birthday']);

            // Refresh the page with a get message to show positive user feedback
            header('Location: edit.php?updatedProfile');
        } else {
            // If email was found in database, display an error for the user
            $feedbackForUser = 'Email ' . $_POST['email'] . ' already belongs to a user.<br>';
        } 
    } else {
        // If the form validation failed, tell the user what went wrong.
        $feedbackForUser = $validator->printAllFeedback();
    }
}

// If the user has requested to update their password
if (isset($_POST['submitPassword'])) {
    // Set color to danger in case the user has just updated something already
    $feedbackColor = 'danger';
    
    // Check if current password is correct, if new and confirm password is the same, and if new password follows the password rules
    $validator->validatePasswordNew($_POST['currentPassword'], $_POST['newPassword'], $_POST['confirmNewPassword'], $userInfo['hashedPassword']);

    if($validator->valid) {
        // If all fields are valid, update the password in the database
        $dbhu->updatePassword($_SESSION['user_id'], $_POST['newPassword']);

        // Refresh the page with a get message to show positive user feedback
        header('Location: edit.php?updatedPassword');
    } else {
        // If the form validation failed, tell the user what went wrong.
        $feedbackForUser = $validator->printAllFeedback();
    }
}

function display() {
global $userInfo;
?>
<!-- Content here -->
<a href="index.php" class="btn btn-secondary active mt-5" role="button">Back to profile</a><br>
<div class="row mt-3">
    <div class="col-md-7">
        <h2>Edit profile details</h2>
        <hr>
        <form action="" method="post">
            <div class="row">
            
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email">Email address <span class="text-danger">*</span></label>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Enter your email" <?php if(!empty($userInfo['email'])) { echo 'value="' . $userInfo['email'] . '"';} ?>>
                        <small class="form-text text-muted">Changing this will change the way you log in</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="firstName">First name <span class="text-danger">*</span></label>
                        <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Enter your first name" <?php if(!empty($userInfo['firstName'])) { echo 'value="' . $userInfo['firstName'] . '"';} ?>>
                        <small class="form-text text-muted">Your first name(s)</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="lastName">Last name <span class="text-danger">*</span></label>
                        <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Enter your last name" <?php if(!empty($userInfo['lastName'])) { echo 'value="' . $userInfo['lastName'] . '"';} ?>>
                        <small class="form-text text-muted">Your last name(s)</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="Enter your location" <?php if(!empty($userInfo['location'])) { echo 'value="' . $userInfo['location'] . '"';} ?>>
                        <small class="form-text text-muted">This can be a country, town or something else</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="telephone">Telephone</label>
                        <input type="text" id="telephone" name="telephone" class="form-control" placeholder="Enter your phone number" <?php if(!empty($userInfo['telephone'])) { echo 'value="' . $userInfo['telephone'] . '"';} ?>>
                        <small class="form-text text-muted">Your phone number (+ and numbers allowed)</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="birthday">Birthday</label><br>
                        <input type="date" id="birthday" name="birthday" <?php if(!empty($userInfo['birthday'])){echo 'value="' . $userInfo['birthday'] . '"';} ?>/><br>
                        <small class="form-text text-muted">Your date of birth</small>
                    </div>
                </div>
            </div>
            <button type="submit" id="submitProfile" name="submitProfile" class="btn btn-primary mt-3 mb-3">Save information</button>
        </form>

            

            
    </div>
    <div class="col-md-1"></div>

    <div class="col-md-4">
        <h2>Change password</h2>
        <hr>
        <form action="" method="post">
            <div class="form-group mb-3">
                <label for="currentPassword">Current password</label>
                <input type="password" id="currentPassword" name="currentPassword" class="form-control" placeholder="Enter your current password">
            </div>

            <div class="form-group mb-3">
                <label for="newPassword">New password</label>
                <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Enter your new password">
                <small class="form-text text-muted">Must be at least 8 characters long, and contain at least 1 number and 1 special character</small>
            </div>

            <div class="form-group mb-3">
                <label for="confirmNewPassword">Confirm new password</label>
                <input type="password" id="confirmNewPassword" name="confirmNewPassword" class="form-control" placeholder="Confirm your new password">
            </div>

            <button type="submit" id="submitPassword" name="submitPassword" class="btn btn-primary mt-3">Change password</button>
        </form>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Profile', $feedbackForUser, $feedbackColor, requireLogin: true);