<?php include_once '../assets/include/template.php';
/*
    With a get request for job listing id, this page is intended to show received job applications.
    This page is only accessible to the job poster. It should show a list of all the applications received for a particular job listing.
    This list should be sorted by date received, with the most recent applications at the top. Each application should be able to be
    "pinned", which will group it at the top of the list. Each application should also be able to be "archived", which will hide it
*/


// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbha = new DBHandlerApplication();

if (!isset($_GET["id"])) {
    // Redirect if no id is provided
    header('Location: ../404.php');
    exit();
}

$applicants = $dbha->getAllListingApplications($_GET["id"]);

function display()
{
    global $applicants;
?>

    <div class="row mt-3">
        <div class="col-md-4">
            <a href="#" type="button" class="btn btn-secondary">Go back to all applications</a>
        </div>
        <div class="col-md-8">
            <h2>Recieved job applications</h2>
        </div>
    </div>
<?php
    if ($applicants) {
        foreach ($applicants as $applicant) {
            echo'
            <div class="card mt-4">
                <div class="card-header">
                    <b>'.$applicant["title"]. '</b>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <p class="card-text">'.$applicant["text"].'</p>
                        </div>
                        <div class="col md-1">
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary">Pin</button>
                                </div>
                                <div class="col-md-5">
                                    <button type="button" class="btn btn-primary">Archive</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">
                                <button type="button" class="btn btn-primary">View</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo "No applicants were found";
    }
}
makePage('display');
