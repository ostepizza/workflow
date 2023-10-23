<?php include_once '../assets/include/template.php';

include("../assets/include/connection.php");

$feedbackForUser = NULL;
$feedbackColor = "danger";

$conn = createDBConnection(); //Connects to the database
session_start();

if (isset($_POST['submit'])) {
    $allConditionsMet = true; //Sets up a fail condition if user-input is bad

    if (!isset($_POST['acceptToS'])) {
        $feedbackForUser .= "You need to accept the terms & conditions.<br>";
        $allConditionsMet = false;
    }

    if (!empty($_POST['email'])) {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $feedbackForUser .= $_POST['email'] . ' is not a valid email.<br>';
            $allConditionsMet = false;
        }
    } else {
        $feedbackForUser .= "You need to enter an email.<br>";
        $allConditionsMet = false;
    }

    if (!empty($_POST['password'])) {
        if(!preg_match('@[0-9]@', $_POST['password'])) {
            $feedbackForUser .= "Password does not contain a number.<br>";
            $allConditionsMet = false;
        }
        if (!preg_match('@[^\w]@', $_POST['password'])) {
            $feedbackForUser .= "Password does not contain a special character.<br>";
            $allConditionsMet = false;
        }
        if (strlen($_POST['password']) < 8) {
            $feedbackForUser .= "Password is not at least 8 characters long.<br>";
            $allConditionsMet = false;
        }
    } else {
        $feedbackForUser .= "You need to enter a password.<br>";
        $allConditionsMet = false;
    }
    
    if (!empty($_POST['firstName'])) {
        if (!preg_match("/^[a-zA-Z-' ]*$/",$_POST['firstName'])) {
            $feedbackForUser .= 'Only letters and white space allowed in first name "' . $_POST['firstName'] . '".<br>';
            $allConditionsMet = false;
        }
    } else {
        $feedbackForUser .= "You need to enter a first name.<br>";
        $allConditionsMet = false;
    }

    if (!empty($_POST['lastName'])) {
        if (!preg_match("/^[a-zA-Z-' ]*$/",$_POST['lastName'])) {
            $feedbackForUser .= 'Only letters and white space allowed in last name "' . $_POST['lastName'] . '".<br>';
            $allConditionsMet = false;
        }
    } else {
        $feedbackForUser .= "You need to enter a last name.<br>";
        $allConditionsMet = false;
    }

    if ($allConditionsMet) {
        /*
            If all form conditions are met, then a SQL statement is prepared.
            This SQL statement checks whether the email is already in the database or not.
            If it returns more than 0 rows, then it exists, and an error is displayed to the user.
        */
        $email = $_POST['email'];
        $email = strtolower($email);

        $sql = 'SELECT count(*) FROM `user` WHERE `email` = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $feedbackForUser .= 'Email ' . $email . ' already belongs to a user.<br>';
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
                $feedbackForUser = "User has been successfully registered. You may now log in.<br>";
                $feedbackColor = "success";
            } else {
                $feedbackForUser = "An error occurred while registering.<br>";
            }
        }
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

makePage('display', 'Register', $feedbackForUser, $feedbackColor);