<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbh = new DBHandlerUser();

// $dbh->updateFirstName($_SESSION['user_id'], 'Jeff'); <-- Method to update user fields. One method per field, more details in dbhandler file

function display() {
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Edit user profile</h1>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Profile', requireLogin: true);