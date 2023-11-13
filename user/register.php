<?php include_once '../assets/include/template.php';

include("../assets/include/connection.php");
$conn = createDBConnection(); //Connects to the database

include_once '../assets/include/validation.php';
$validator = new Validator();

$feedbackForUser = NULL;
$feedbackColor = "danger";

if (isset($_POST['submit'])) {
    // Give ToS-checkmark a value even if user hasn't specified. Else the post-value is missing and validation won't work
    $tos = (isset($_POST['acceptToS'])) ? $_POST['acceptToS'] : false;
    
    // Pass all form elements to the Validator
    $validator->validateRegistration($tos, $_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName']);

    if ($validator->valid) {
        // Convert the posted email to lowercase, for consistency
        $email = strtolower($_POST['email']);

        /*
            If all form conditions are met, then a SQL statement is prepared.
            This SQL statement checks whether the email is already in the database or not.
            If it returns more than 0 rows, then it exists, and an error is displayed to the user.
        */
        $sql = 'SELECT count(*) FROM `user` WHERE `email` = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $feedbackForUser = 'Email ' . $email . ' already belongs to a user.<br>';
        } else {
            /*
                If the email is not present in the database, the registration continues.
                It first hashes a password to more safely store the password in case of a data breach.
                It then converts the first and last names to start with a large letter, followed by small letters.
                It then prepares and executes a SQL statement to put the user info into the user-table.
            */
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $firstname = $_POST['firstName'];
            $firstname = strtolower($firstname);
            $firstname = ucwords($firstname);

            $lastname = $_POST['lastName'];
            $lastname = strtolower($lastname);
            $lastname = ucwords($lastname);

            $sql = 'INSERT INTO `user` (`email`, `password`, `first_name`, `last_name`) VALUES (?, ?, ?, ?)';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $email, $hashed_password, $firstname, $lastname);

            if ($stmt->execute()) {
                $stmt->close();
                $feedbackForUser = "User has been successfully registered. You may now log in.<br>";
                $feedbackColor = "success";
            } else {
                $stmt->close();
                $feedbackForUser = "An error occurred while registering.<br>";
            }
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