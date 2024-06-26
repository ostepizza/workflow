<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();
$dbhc = new DBHandlerCompany();
$dbha = new DBHandlerApplication();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Set up empty feedback
$feedbackForUser = NULL;
$feedbackColor = "danger";

// Retrieve the users info
if(!empty($_SESSION['user_id'])) {
    $userInfo = $dbhu->selectAllUserInfoByUserId($_SESSION['user_id']);
    if ($dbhc->getCompanyDetailsFromUserId($_SESSION['user_id'])) {
        $userInfo = array_merge($userInfo, $dbhc->getCompanyDetailsFromUserId($_SESSION['user_id']));
    }
    $dbha->deleteAnyDraftsPastDeadline($_SESSION['user_id']);
    $applications = $dbha->getAllUserApplications($_SESSION['user_id']);
}

if (isset($_GET['toggleSearchable'])) {
    $dbhu->toggleSearchable($_SESSION['user_id']);
    header('Location: index.php?updatedSearchable');
} else if (isset($_GET['updatedSearchable'])) {
    if ($userInfo['searchable']) {
        $feedbackForUser = 'You are now searchable to employers.<br>';
        $feedbackColor = 'success';
    } else {
        $feedbackForUser = 'You are no longer searchable to employers.<br>';
    }
} else if (isset($_GET["updatedCompetence"])) {
    $feedbackForUser = 'Your competence has been updated.<br>';
    $feedbackColor  = 'success';
} else if (isset($_GET["deletedApplication"])) {
    $feedbackForUser = 'The application has been deleted.<br>';
    $feedbackColor  = 'warning';
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
global $userInfo, $applications;

$userInfo['location'] = $userInfo['location'] ?? 'No location added';
$userInfo['telephone'] = $userInfo['telephone'] ?? 'No phone added';
$birthday = ($userInfo['birthday'] !== NULL) ? date('d. M Y', strtotime($userInfo['birthday'])) : 'No birthday added';

?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-2">
        <?php 
        if ($userInfo['picture']) {
            echo '<img src="../assets/img/user/' . $userInfo['picture'] . '" class="img-fluid rounded border border-secondary" alt="Your profile picture">';
        } else {
            echo '<img src="../assets/img/user/default.jpg" alt="The default user profile picture" class="img-fluid rounded border border-secondary">';
        }

        if($userInfo['cv'] != NULL) {
            // Button is currently non functional
            echo '<a href="../assets/pdf/user/'.$userInfo['cv'].'" class="btn btn-primary mt-3 w-100" role="button" target=”_blank”>Open resume</a>';
        }
        ?>
        <a href="user_files.php" class="btn btn-secondary mt-3 w-100" role="button" data-bs-placement="top" data-bs-toggle="tooltip" title="Your user files are your profile image and resume PDF.">Edit user files</a><br>
        <a href="edit.php" class="btn btn-secondary mt-3 w-100" role="button">Edit profile</a><br>
        
        
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
        <h2 class="mb-3">Applications:</h2>
        <?php 
        if ($applications) {
            foreach ($applications as $application) {
                if ($application['sent'] == 0 && $application['deadline'] < date('Y-m-d')) {
                    continue;
                } 

                if ($application['sent'] == 1) {
                    $status = '<span class="badge bg-success">Sent</span>';
                } else {
                    $status = '<span class="badge bg-warning">Draft</span>';
                }
                ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <?php echo '<b>' . $application['title'] . '</b> (To ' . $application['company_name'] . ') ' . $status; ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <?php
                                if (strlen($application['text']) > 175) {
                                    echo substr($application['text'], 0, 175) . '...';
                                } else {
                                    echo $application['text'];
                                }
                                ?>
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-6 px-1">
                                        <?php 
                                            if ($application['sent'] == 0) {
                                                echo '<a href="../applications/edit.php?id=' . $application['id'] . '" class="btn btn-secondary w-100" role="button">Edit</a>';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-6 px-1">
                                        <a href="../applications/view.php?id=<?php echo $application['id']; ?>" class="btn btn-primary w-100 ml-2" role="button">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p>You have not applied to any jobs yet.</p>';
        }
        ?>
        
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