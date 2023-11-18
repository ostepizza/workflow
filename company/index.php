<?php include_once '../assets/include/template.php';

function display() {
?>
<!-- Content here -->
<!-- If not a part of a company -->
<?php

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();

// If the page is loaded with a get request, display a message depending on the request
if(!empty($_GET)){
    $getMsg = NULL;
    $getMsgColor = NULL;
    if(isset($_GET['deletedCompany'])) {
        $getMsg = 'Company has been deleted.';
        $getMsgColor = 'danger';
    } else if (isset($_GET['registerSuccess'])) {
        $getMsg = 'Company successfully registered.';
        $getMsgColor = 'success';
    }
    echo('<div class="alert alert-' . $getMsgColor . ' mt-3" role="alert">' . $getMsg . '</div>');  
}

// Check if the user is a part of a company
if ($companyId = $dbhc->getCompanyIdFromUserId($_SESSION['user_id'])){
    // If member is in a company, retrieve company data
    $companyDetails = $dbhc->getCompanyDetailsFromCompanyId($companyId);
    $companyName = $companyDetails['companyName'];
    $companyDescription = $companyDetails['companyDescription'];

    // TODO: Here, retrieve statistics about job listings etc for display in the dashboard

    // Display the company dashboard:
    echo '
    <div class="row mt-5">
        <div class="col-md-12">
            <h1>' . $companyName . ' - Company dashboard</h1>
            <p>' . $companyDescription . '</p>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-7">
            <h2>Active job listings (Amount):</h2>
            <!-- TBD: sorted by deadline -->
            <hr>
            <div class="card">
                <div class="card-header">
                    Job listing title (X views)
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <p>X applications received.</p>
                            <p>Deadline X-date</p>
                            <p>This listing was published X-date</p>
                        </div>
                        <div class="col-md-3">
                            <a href="#"><button type="button" class="btn btn-primary w-100">View applications</button></a>
                            <a href="#"><button type="button" class="btn btn-secondary w-100 mt-2">View listing</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-1">
        </div>

        <div class="col-md-3">
            <div class="row">
                <h2>Manage</h2>
                <hr>
                <a href="../jobs/new.php"><button type="button" class="btn btn-primary w-100">Create new job listing</button></a>
                <a href="view_members.php"><button type="button" class="btn btn-secondary w-100 mt-2">View members of company</button></a>
                <a href="edit.php"><button type="button" class="btn btn-secondary w-100 mt-2">Edit company details</button></a>
            </div>
            <div class="row mt-3">
                <h2>Statistics</h2>
                <hr>
                <p>
                    Total listings: X<br>
                    Total applications received: X<br>
                </p>
            </div>
        </div>
    </div>
    ';
} else {
    // If not part of a company
    echo '
    <div class="row mt-5">
        <div class="col-md-12">
            <h1>Company dashboard</h1>
            <p>This section is used for companies intending to use this platform for creating job applications and finding new employees.</p>
            <p><b>You are currently not a member of a company. <a href="new.php">Create one</a>, or wait to be added to one.</b></p>
        </div>
    </div>
    ';
}
?>
<!-- Content here -->
<?php
}

makePage('display', 'Companies', requireLogin: true);