<?php include_once '../assets/include/template.php';

/*
    This page creates a new job application for a particular job listing in the database.
    It first checks if the job listing is public and not past deadline, if not it redirects.
    It then creates a new application in the database, and redirects the user to the edit page for that application.
*/

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhl = new DBHandlerListing();
$dbha = new DBHandlerApplication();

if (!isset($_GET["listingId"])) {
    // Redirect if no listing id is provided
    header('Location: ../404.php');
    exit();
}
$listingId = $_GET["listingId"];

// Check if the listing is public
if ($published = $dbhl->isListingPublished($listingId) && $deadlineNotPassed = $dbhl->isListingDeadlineNotPassed($listingId)) {
    if ($applicationId = $dbha->createNewApplication($listingId, $_SESSION['user_id'])) {
        // Redirect if successfully created a new application
        header('Location: edit.php?id=' . $applicationId);
        exit();
    }
} else {
    // Redirect if listing is not public or deadline has passed
    header('Location: ../403.php');
    exit();
}

function display()
{
// Display a page if the redirect doesn't work
echo '
    <h1>An error occurred.</h1>
    <p>Please try again later.</p>
    ';
}

makePage('display', 'New application', requireLogin: true);
?>