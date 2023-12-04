<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();
$dbhc = new DBHandlerCompany();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();


$feedbackForUser = NULL;
$feedbackColor = "danger";

// Retrieve the users info
if(!empty($_SESSION['user_id'])) {
    $userInfo = $dbhu->selectAllUserInfoByUserId($_SESSION['user_id']);
    $userInfo = array_merge($userInfo, $dbhc->getCompanyDetailsFromUserId($_SESSION['user_id']));
}

if (isset($_GET['toggleSearchable'])) {
    $dbhu->toggleSearchable($_SESSION['user_id']);
    header('Location: index.php?updatedSearchable');
} else if (isset($_GET['updatedSearchable'])) {
    if ($userInfo['searchable']) {
        $feedbackForUser = 'You are now searchable to employers.';
        $feedbackColor = 'success';
    } else {
        $feedbackForUser = 'You are no longer searchable to employers.';
    }
} else if (isset($_GET["updatedCompetence"])) {
    $feedbackForUser = 'Your competence has been updated';
    $feedbackColor  = 'success';
}

if(isset($_POST["updateComp"])) {
    $validator->validateCompetence($_POST["compField"]);
    if ($validator->valid) {
        if ($dbhu->updateCompetence($_SESSION["user_id"], strip_tags($_POST["compField"]))) {
            header('Location: index.php?updatedCompetence');
            exit();
        }
    } else {
         // If the form validation failed, tell the user what went wrong.
         $feedbackForUser = $validator->printAllFeedback();
         $feedbackColor = 'danger';
    }
}

function display() {
global $userInfo;

$userInfo['location'] = $userInfo['location'] ?? 'No location added';
$userInfo['telephone'] = $userInfo['telephone'] ?? 'No phone added';
$birthday = ($userInfo['birthday'] !== NULL) ? date('d. M Y', strtotime($userInfo['birthday'])) : 'No birthday added';

?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-2">
        <?php 
        if ($userInfo['picture']) {
            $base64Image = base64_encode($userInfo['picture']);
            $pictureData = "data:image/jpeg;base64," . $base64Image;
            echo '<img src="' . $pictureData . '" alt="Your profile picture" class="img-fluid rounded border border-secondary">';
        } else {
            echo '<img src="../assets/img/user/default.jpg" alt="The default user profile picture" class="img-fluid rounded border border-secondary">';
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
                Email: <?php echo $userInfo['email']; ?>
            </div>
            <div class="col-md-4">
                Telephone: <?php echo $userInfo['telephone']; ?>
            </div>
            <div class="col-md-4">
                Birthday: <?php echo $birthday; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if (!empty($userInfo['companyName'])) { echo '<b>Member of <a href="../company/index.php">' . $userInfo['companyName'] . '</a></b>'; }; ?>
            </div>
        </div>
        <br>
        <h2>Competence:</h2>
        <form action="" method="post">
        <div class="form-group">
            <textarea class="form-control" name="compField" rows="6"><?php if($userInfo["competence"] !== NULL) { echo $userInfo["competence"]; } ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-11">
            </div>
            <div class="col-md-1">
                <button type="submit" name="updateComp" class="btn btn-primary mt-2">Save</button>
            </div>
        </div>
    
        </form>
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