<?php include_once '../assets/include/template.php';
/*
    With a get request for job listing id, this shows received job applications.
    This page is only accessible to the job poster. 
    It shows a list of all the applications received for a particular job listing.
    This list is sorted by date received, with the most recent applications at the top.
    Each application is able to be "pinned", which groups it at the top of the list. 
    Each application is also able to be "archived", which hides it
*/

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();
$dbhl = new DBHandlerListing();
$dbha = new DBHandlerApplication();

// Default feedback for user
$feedbackForUser = NULL;
$feedbackColor = "danger";

// Redirect if no listing ID is provided
if (!isset($_GET["id"])) {
    // Redirect if no id is provided
    header('Location: ../404.php');
    exit();
}

$listingId = intval($_GET["id"]);

// Access control, first check if listing exists
if ($listingId = $dbhl->getListing($listingId)) {
    // Check if user is a part of the company that owns the listing
    $companyId = $dbhc->getCompanyIdFromUserId($_SESSION['user_id']);
    if ($companyId != $listingId['companyId']) {
        // If not, redirect
        header('Location: ../403.php');
        exit();
    }

} else {
    // Redirect if listing doesn't exist
    header('Location: ../403.php');
    exit();
}

// Handle the toggle admin and remove user forms
if (isset($_POST['applicationId']) && isset($_POST['action'])) {
    //Make sure the applicationId is an integer
    $applicationToManage = intval($_POST['applicationId']);

    // Check if the application belongs to the listing
    if ($dbha->checkApplicationIdAndCompany($applicationToManage, $companyId)) {
        // If the application belongs to the listing, check if the action is pin or archive
        if ($_POST['action'] == 'pin') {
            // Make a call to db
            $dbha->toggleApplicationPinned($applicationToManage);
        } else if ($_POST['action'] == 'archive') {
            // Make a call to db
            $dbha->toggleApplicationArchived($applicationToManage);
        }

    } else {
        $feedbackForUser = 'Application does not belong to company.';
        $feedbackColor = 'danger';
    }

    
}

// Retrieve all applications for the listing, both pinned and normal
$applicants = $dbha->getAllListingApplications($_GET["id"]);
$pinnedApplicants = $dbha->getAllListingApplications($_GET["id"], false, true);

function display() {
global $applicants, $pinnedApplicants;
?>
    <a href="../company/index.php"><button type="button" class="btn btn-secondary mb-3 mt-5"><i class="fa-solid fa-circle-left"></i> Return to dashboard</button></a>

    <div class="row mt-3">
        <div class="col-md-12">
            <h1>Recieved job applications</h1>
            <a href="received_archived.php?id=<?php echo $_GET["id"] ?>" class="btn btn-primary mb-3 mt-3">View archived applications <i class="fa-solid fa-eye"></i></a>
        </div>
    </div>
<?php
    if ($pinnedApplicants) {
        echo '<h2>Pinned applications</h2>';
        foreach ($pinnedApplicants as $applicant) {
            ?>
            <div class="card mt-4">
                <div class="card-header">
                    <b><?php echo $applicant["title"]; ?></b>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <?php
                            if (strlen($applicant['text'] > 500)) {
                                echo '<p class="card-text">' . substr($applicant["text"], 0, 500) . '...</p>';
                            } else {
                                echo '<p class="card-text">' . $applicant["text"] . '</p>';
                            }
                            ?>
                        </div>
                        <div class="col md-1">
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST">
                                        <input type="hidden" name="applicationId" value="<?php echo $applicant['id'] ?>">
                                        <input type="hidden" name="action" value="pin">
                                        <button type="submit" class="btn btn-danger w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove application pin"><i class="fa-solid fa-circle-minus"></i></button>
                                    </form>
                                </div>  
                                <div class="col-md-6">
                                    <form method="POST">
                                        <input type="hidden" name="applicationId" value="<?php echo $applicant['id'] ?>">
                                        <input type="hidden" name="action" value="archive">
                                        <button type="submit" class="btn btn-warning w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Archive application"><i class="fa-solid fa-box-archive"></i></i></button>
                                    </form>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <a href="view.php?id=<?php echo $applicant['id'] ?>" class="btn btn-primary w-100">View <i class="fa-solid fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '
        <br>
        <hr>
        <h2>Normal applications</h2>';
    }
    if ($applicants) {
        foreach ($applicants as $applicant) {
            ?>
            <div class="card mt-4">
                <div class="card-header">
                    <b><?php echo $applicant["title"]; ?></b>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <?php
                            if (strlen($applicant['text'] > 500)) {
                                echo '<p class="card-text">' . substr($applicant["text"], 0, 500) . '...</p>';
                            } else {
                                echo '<p class="card-text">' . $applicant["text"] . '</p>';
                            }
                            ?>
                        </div>
                        <div class="col md-1">
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST">
                                        <input type="hidden" name="applicationId" value="<?php echo $applicant['id'] ?>">
                                        <input type="hidden" name="action" value="pin">
                                        <button type="submit" class="btn btn-success w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Pin application to top"><i class="fa-solid fa-thumbtack"></i></button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST">
                                        <input type="hidden" name="applicationId" value="<?php echo $applicant['id'] ?>">
                                        <input type="hidden" name="action" value="archive">
                                        <button type="submit" class="btn btn-warning w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Archive application"><i class="fa-solid fa-box-archive"></i></button>
                                    </form>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <a href="view.php?id=<?php echo $applicant['id'] ?>" class="btn btn-primary w-100">View <i class="fa-solid fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<i>This job listing has not yet received any applications.</i>";
    }
}
makePage('display', 'Received applications', $feedbackForUser, $feedbackColor, requireLogin: true);
