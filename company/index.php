<?php include_once '../assets/include/template.php';

function display() {
?>
<!-- Content here -->
<!-- If not a part of a company -->
<?php
include("../assets/include/connection.php");
$conn = createDBConnection(); // Connects to the database

$memberOfCompany = false;
$company_id = NULL;
$user_id = $_SESSION['user_id'];

// Ask database if logged in member is found in the company_management table
$sql = 'SELECT `company_id` FROM `company_management` WHERE `user_id` = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
if ($stmt->execute()) {
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If any rows are found, the user is a part of a company
        $memberOfCompany = true;
        $stmt->bind_result($company_id);
        $stmt->fetch();
    }
    $stmt->close();
}

if ($memberOfCompany){
    // If member is in a company, retrieve company data
    $sql = 'SELECT `name`, `description` FROM `company` WHERE `id` = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $company_id);
    if ($stmt->execute()) {
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($company_name, $company_description);
            $stmt->fetch();
        } else {
            $userFeedback = "An error occurred while retrieving company data";
        }
        $stmt->close();
    }


    // Display the company dashboard:
    echo '
    <div class="row mt-5">
        <div class="col-md-12">
            <h1>Company dashboard - ' . $company_name . '</h1>
            <p>' . $company_description . '</p>
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
                <a href="edit.php"><button type="button" class="btn btn-secondary w-100 mt-2">Edit company</button></a>
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
            <p>You are currently not a member of a company. <a href="new.php">Create one</a>, or wait to be added to one.</p>
        </div>
    </div>
    ';
}
?>
<!-- Content here -->
<?php
}

makePage('display', 'Companies', requireLogin: true);