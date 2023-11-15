<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbh = new DBHandlerUser();

$feedbackForUser = NULL;
$feedbackColor = "danger";

// Get userinfo and display it for debugging reasons while making the page
if($userInfo = $dbh->selectAllUserInfoByUserId($_SESSION['user_id'])) {
    //print_r($userInfo);
}

if (isset($_GET['toggleSearchable'])) {
    $dbh->toggleSearchable($_SESSION['user_id']);
    header('Location: index.php?updatedSearchable');
} if (isset($_GET['updatedSearchable'])) {
    if($userInfo['searchable']) {
        $feedbackForUser = 'You are now searchable to employers.';
        $feedbackColor = 'success';
    } else {
        $feedbackForUser = 'You are no longer searchable to employers.';
    }
    

}

function display() {
global $userInfo;



$userInfo['location'] = $userInfo['location'] ?? 'No location added';
$userInfo['telephone'] = $userInfo['telephone'] ?? 'No phone added';
$userInfo['competence'] = $userInfo['competence'] ?? 'You have not written anything about your competence.';
$birthday = ($userInfo['birthday'] !== NULL) ? date('d. M Y', strtotime($userInfo['birthday'])) : 'No birthday added';

?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-2">
        <?php 
        if ($userInfo['picture']) {
            $base64Image = base64_encode($userInfo['picture']);
            $pictureData = "data:image/jpeg;base64," . $base64Image;
            echo '<img src="' . $pictureData . '" alt="Your profile picture" class="img-fluid rounded">';
        } else {
            echo '<img src="../assets/img/user/default.jpg" alt="The default user profile picture" class="img-fluid rounded">';
        }
        ?>
        <a href="edit.php" class="btn btn-primary active mt-3" role="button">Edit profile</a><br>
        <?php
        if($userInfo['cv'] != NULL) {
            // Button is currently non functional
            echo '<a href="#" class="btn btn-primary active mt-3" role="button">Open resume</a>';
        }
        ?>
    </div>

    <div class="col-md-1"></div>

    <div class="col-md-9">
        <p>
            <span class="h1"><?php echo $userInfo['firstName'] . ' ' . $userInfo['lastName']; ?> </span><span class="h7"><?php echo $userInfo['location']; ?></span><br>
            <form>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="searchableSwitch" data-bs-toggle="tooltip" data-bs-placement="left" title="If this is switched on, companies looking for new employees can find your name and email, even if you haven't applied to their job listing."
                    <?php 
                        if ($userInfo['searchable']) {
                            echo 'checked';
                        }
                    ?>
                    >
                    <label class="form-check-label" for="flexSwitchCheckChecked">Discoverable to employers</label>
                </div>
            </form>
        </p>
        <hr>
        <div class="row">
            <div class="col-md-4">
                Birthday: <?php echo $birthday; ?>
            </div>
            <div class="col-md-4">
                Telephone: <?php echo $userInfo['telephone']; ?>
            </div>
            <div class="col-md-4">
                nothing LOL
            </div>
        </div>
        <br>
        <h2>Competence:</h2>
        <p>
            <?php echo $userInfo['competence']; ?>
        </p>
        <hr class="mb-5">
        <h2>Jobs you've applied to:</h2>
        a table
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('searchableSwitch');

        checkbox.addEventListener('change', function() {
            // Reload the page with the toggleSearchable parameter reflecting the current state
            window.location.href = 'index.php?toggleSearchable=' + (this.checked ? 'true' : 'false');
        });
    });
</script>

<!-- Content here -->
<?php
}

makePage('display', 'Profile', $feedbackForUser, $feedbackColor, requireLogin: true);