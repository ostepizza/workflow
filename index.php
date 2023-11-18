<?php include_once 'assets/include/template.php';

function display() {
?>
<!-- Content here -->
<?php
if(isset($_GET['loggedout'])) {
    echo('
    <div class="alert alert-warning mt-3" role="alert">
        You have been successfully logged out. 
    </div>
    ');
} else if (isset($_GET['loggedin'])) {
    echo('
    <div class="alert alert-success mt-3" role="alert">
        You have been successfully logged in. 
    </div>
    ');
}
?>
<div class="row mt-5">
    <div class="col-md-7">
        <h1>Welcome to Workflow!</h1>
        <p>We currently have X job listings from X companies in our system, and X applications sent to employers!</p>
        <br>
        <p>Some personal stats if logged in</p>
    </div>
    
    <div class="col-md-2">
    </div>
    
    <div class="col-md-3">
        <h2>Useful tips</h2>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Want to find your next job?</h5>
                <p class="card-text">Go and check out available job listings and apply today!</p>
                <a href="jobs/index.php" class="btn btn-primary">Job listings</a>
            </div>
        </div>

        <?php
        if(isset($_SESSION['user_id'])) {
            echo('
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Become discoverable to employers</h5>
                <p class="card-text">Make your profile searchable to potential employers by changing your profile and updating your fields of expertise!</p>
                <a href="user/index.php" class="btn btn-primary">View profile</a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Want to create a job listing?</h5>
                <p class="card-text">Go to the company dashboard to create a job listing, or use the link at the bottom of the page.</p>
                <a href="company/index.php" class="btn btn-primary">Company dashboard</a>
            </div>
        </div>');
        }
        ?>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Home');