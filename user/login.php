<?php include_once '../assets/include/template.php';

include("../assets/include/connection.php");
$conn = createDBConnection(); //Connects to the database

$feedbackForUser = NULL;
$feedbackColor = "danger";

if (isset($_POST['submit'])) {
    $allConditionsMet = true; //Sets up a fail condition if user-input is bad

    if (!empty($_POST['email'])) {
        //Check the email before sending a query to SQL server to reduce query attempts
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $feedbackForUser .= $_POST['email'] . ' is not a valid email.<br>';
            $allConditionsMet = false;
        }
    } else {
        $feedbackForUser .= "You need to enter an email.<br>";
        $allConditionsMet = false;
    }

    if (empty($_POST['password'])) {
        $feedbackForUser .= "You need to enter a password.<br>";
        $allConditionsMet = false;
    }

    if ($allConditionsMet) {
         /*
            If all form conditions are met, then a SQL statement is prepared.
            This SQL statement looks for the user id and password where there's a matching email as from the form
            It then compares the password retrieved with the password input in the form.
            If the password is correct, the user id is set in the session,
            and the user is redirected to the home page with a success message.
        */
        $email = $_POST['email'];
        $email = strtolower($email);
    
        $sql = 'SELECT `id`, `password` FROM `user` WHERE `email` = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        if ($stmt->execute()) {
            $stmt->store_result();
    
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $hashed_password);
                $stmt->fetch();
    
                // Compare the provided password with the stored hashed password
                if (password_verify($_POST['password'], $hashed_password)) {
                    $feedbackForUser = "Login successful!";
                    $feedbackColor = "success";

                    $_SESSION['user_id'] = $user_id;
                    $stmt->close();

                    header('Location: ../index.php?loggedin');
                } else {
                    $feedbackForUser = "Password is incorrect.";
                }
            } else {
                $feedbackForUser = "Email is not tied to an account.";
            }
        } else {
            $feedbackForUser = "An error occurred while logging in.";
        }
        $stmt->close();
    }
}

function display() {
?>
    <!-- Oppgave her -->
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
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
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
    <!-- Oppgave her -->
<?php
}

makePage('display', 'Log in', $feedbackForUser, $feedbackColor, requireNoUser: true);