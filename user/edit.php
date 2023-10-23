<?php include_once '../assets/include/template.php';

include("../assets/include/connection.php");

function display() {

$conn = createDBConnection(); //Connects to the database
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