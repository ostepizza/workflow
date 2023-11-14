<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbh = new DBHandlerUser();

// Get userinfo and display it for debugging reasons while making the page
if($userInfo = $dbh->selectAllUserInfoByUserId($_SESSION['user_id'])) {
    print_r($userInfo);
}

function display() {
global $userInfo;
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1><?php echo $userInfo['firstName'] . ' ' . $userInfo['lastName']; ?> <a href="edit.php" class="btn btn-primary active" role="button">Edit profile</a></h1><br>
        <?php 
        $birthday = strtotime($userInfo['birthday']);
        echo 'Birthday: ' . date('d. M Y', $birthday) . '<br>';


        
        ?>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Profile', requireLogin: true);