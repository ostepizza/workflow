<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbh = new DBHandler();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

$feedbackForUser = NULL;
$feedbackColor = "danger";

if (isset($_POST['submit'])) {

    // Validate the login form inputs
    $validator->validateLogin($_POST['email'], $_POST['password']);

    // If the form inputs are valid
    if ($validator->valid) {
        // Attempt to log in user with the supplied email and password
        if ($dbh->loginUser($_POST['email'], $_POST['password']) != false) {
            // If successful login, userid is now in session. Redirect from login page.
            header('Location: ../index.php?loggedin');
        } else {
            // User has input a non-existent email, wrong email and/or password. Tell them.
            $feedbackForUser = 'Wrong email and/or password.<br>';
        }
        // if ($dbh->loginUser == false) {handle error} else set ID in session
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
        <h1>Log in</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="text" id="email" name="email" class="form-control" placeholder="Enter your email" autofocus>
            </div>

            <div class="form-group mt-3">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password">
            </div>

            <!--<div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember</label>
            </div>-->

            <button type="submit" id="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>

    <div class="col-md-2"></div>

    <div class="col-md-3">
        <h2>No account?</h2>
        <a class="btn btn-primary" href="register.php" role="button">Register new account</a>
    </div>
    
    <div class="col-md-2"></div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Log in', $feedbackForUser, $feedbackColor, requireNoUser: true);