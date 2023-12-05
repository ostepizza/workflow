<?php include_once '../assets/include/template.php';

/*
    check if a) listing is owned by user or
    b) user belongs to company that owns listing AND application is sent
*/

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();
$dbha = new DBHandlerApplication();

$feedbackForUser = NULL;
$feedbackColor = 'danger';

if (isset($_GET['sentApplication'])) {
    $feedbackForUser = 'Application has successfully been sent.';
    $feedbackColor = 'success';
}

// Retrieve the application id from the get request
$applicationId = $_GET["id"];

// Set up empty status
$status = NULL;

if ($application = $dbha->getApplication($applicationId)) {
    // If an application is found, check the ownership

    if ($application['user_id'] == $_SESSION['user_id']) {
        // Do nothing, as the user is the owner of the application
        // Give user some feedback
        if ($application['sent'] == 1) {
            $status = '<span class="badge bg-success">Sent</span>';
        } else {
            $status = '<span class="badge bg-warning">Draft</span>';
        }

    } else if ($dbhc->getCompanyIdFromUserId($_SESSION['user_id']) == $application['company_id'] && $application['sent'] == 1) {
        // Do nothing, as the application is sent, and the user belongs to the company that owns the listing
    } else {
        // If the user is not the owner of the application, and does not belong to the company that owns the listing, redirect to 403
        header('Location: ../403.php');
        exit();
    }

} else {
    // If no application is found, redirect to 403 to not give away information about the existence of any applications
    header('Location: ../403.php');
    exit();
}

$application['title'] = (isset($application['title']) && $application['title'] !== '') ? $application['title'] : 'Missing title';
$application['text'] = (isset($application['text']) && $application['text'] !== '') ? $application['text'] : 'Missing text';
$application['competence'] = (isset($application['competence']) && $application['competence'] !== '') ? $application['competence'] : 'Missing competence';

function display() {
global $application, $status;
?>
<a href="javascript:history.back()" class="btn btn-secondary mt-5" role="button">Back</a><br>
<div class="row mt-3">
    <div class="col-md-12">
        <span class="h1">Application</span> <?php echo $status; ?>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <p>
                    <b>From:</b><br>
                    <?php
                    echo $application['first_name'] . ' ' . $application['last_name'] . '<br>';
                    echo '<a href="mailto:' . $application['email'] . '">' . $application['email'] . '</a><br>';
                    echo $application['telephone'] . '<br>';
                    echo $application['location'] . '<br>';
                    ?>
                </p>
            </div>
            <div class="col-md-6">
                <p>
                    <b>To:</b><br>
                    <?php
                    echo $application['company_name'] . '<br>';
                    echo '<i>regarding listing</i><br>';
                    echo '"' . $application['listing_name'] . '"<br>';
                    ?>
                </p>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2><?php echo $application['title']?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p>
                    <?php echo nl2br($application['text'])?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-1"></div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-center">
                <?php
                if ($application['picture']) {
                    echo '<img src="../assets/img/user/'.$application['picture'].'" alt="The default profile picture" class="img-fluid rounded border border-secondary w-50 m-2">';
                } else {
                    echo '<img src="../assets/img/user/default.jpg" alt="The default profile picture" class="img-fluid rounded border border-secondary w-50 m-2">';
                }
                ?>
                <br>
                <span class="h5"><?php echo $application['first_name'] . ' ' . $application['last_name'];?></span>
            </div>
            <div class="card-body">
                <p class="card-text h6 text-center">Competence</p>
                <p class="card-text">
                    <?php echo nl2br($application['competence'])?>
                </p>
            </div>
        </div>
    </div>
</div>


<?php

}
makePage('display', 'View Application', $feedbackForUser, $feedbackColor, requireLogin: true);
?>