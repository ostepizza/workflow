<?php include_once '../assets/include/template.php';

//Check if a session exists, and if not, start one so you have one to destroy
if (!session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset and destroy the session
unset($_SESSION);
session_destroy();

//Redirect to the homepage with a logout-message
header('Location: ../index.php?loggedout');

//In case the redirect goes wrong, display a page:
function display() {
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Logged out.</h1>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Logged out');