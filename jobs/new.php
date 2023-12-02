<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhc = new DBHandlerCompany();
$dbhl = new DBHandlerListing();

// Check if the user is a part of a company
if ($company = $dbhc->getCompanyDetailsFromUserId($_SESSION['user_id'])) {
    // Create a listing and retrieve the listing ID, then redirect
    if ($listingId = $dbhl->createNewListing($company['companyId'])) {
        // Redirect if successfully created a new listing
        header('Location: edit.php?id=' . $listingId);
    }
} else {
    // Redirect if user isn't in a company
    header('Location: ../403.php');
}

function display()
{
// Display a page if the redirect doesn't work
echo '
    <h1>An error occurred.</h1>
    <p>Please try again later.</p>
    ';
}

makePage('display', 'New listing', requireLogin: true);
