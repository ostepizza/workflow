<?php include_once 'assets/include/template.php';

// Include and establish connection with DB
include_once 'assets/include/DBHandler.php';
$dbhs = new DBHandlerStatistics();
$dbhc = new DBHandlerCompany();

function display() {
global $dbhs, $dbhc;
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
    <div class="col-md-8">
        <h1>Welcome to Workflow!</h1>
        <p>We currently have <b><?php echo $dbhs->getSystemPublishedListingsCount(); ?> job listings</b> from <b><?php echo $dbhs->getSystemTotalCompanies(); ?> companies</b> in our system, and <b><?php echo $dbhs->getSystemTotalApplicationsSent(); ?> applications</b> sent to employers!</p>
        <?php
        if (isset($_SESSION['user_id'])) {
            ?>
            <br>
            <hr>
            <br>
            <div class="text-center">
                <p>
                    <span class="h3">
                        You have sent <b><?php echo $dbhs->getUserTotalApplicationsSent($_SESSION['user_id']) ?> applications</b> to employers
                    </span>
                </p>
            </div>
            <?php
            if ($companyId = $dbhc->getCompanyIdFromUserId($_SESSION['user_id'])) {
                $companyDetails = $dbhc->getCompanyDetailsFromCompanyId($companyId);
                $companyStatistics = $dbhs->getCompanyStatistics($companyId);
                echo '
                <br>
                <hr>
                <br>
                <div class="text-center">
                        <span class="h1">'.$companyDetails['companyName'].' statistics:</span><br>
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <div class="card p-5">
                                Currently <b>' . $companyStatistics['published_listings'] . ' job listings</b> published<br>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-5">
                                Received <b>' . $companyStatistics['received_applications'] . ' applications</b> from job seekers<br>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-5">
                                <b>' . $companyStatistics['total_views'] . ' views</b> on all published job listings<br>
                            </div>
                        </div>
                    </div>
                </div>
                ';
            }
        }
        ?>

    </div>
    
    <div class="col-md-1">
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