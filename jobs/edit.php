<?php
/*
    Edit will be used to edit a job listing.
    Before accessing, the user will be checked towards two things:
        1) Is the user logged in? If not, redirect
        2) Is the user a part of the company that owns the job listing? If not, redirect
    The form will then be populated with the current data from the database, through a GET request.
    The user can also PREVIEW the job listing, through the listing.php page
*/



include_once '../assets/include/template.php';

function display()
{
?>
    <!-- Content here -->


<?php
}

makePage('display', 'Edit job listing', requireLogin: true);
