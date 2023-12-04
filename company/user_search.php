<?php
include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Set up default feedback
$feedbackForUser = NULL;
$feedbackColor = "danger";

if (isset($_POST["search"])) {
    // Needs to be validated:
    $validator->validateSearch($_POST["searchfield"]);
    $validator->validateSearchMinChar($_POST["searchfield"]);

    if ($validator->valid) {
        $searched = $dbhu->searchForUser($_POST["searchfield"]);
    } else {
        // If the form validation failed, tell the user what went wrong.
        $feedbackForUser = $validator->printAllFeedback();
        $feedbackColor = 'danger';
    }
}

function display() {
global $searched;
?>

<div class="row mt-5">
    <div class="col-md-12">
        <a href="index.php"><button type="button" class="btn btn-secondary mb-3">&lt; Return to dashboard</button></a>
        <h1>User search</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <p>
            <b>This page is used to search for users by free text.</b><br> 
            This means that you can look up users by searching by name, email, or what they have written about their competence.<br>
            Only users that have consented to being searchable will be displayed.<br>
            It is intended to be used to look for potential candidates for a job listing.
        </p>
</div>

<div class="row">
    <div class="col-md-12">
        <p class="h5">Search for users</p>
        <form action="" method="post">
            <div class="row mt-2">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control" name="searchfield" placeholder="Example: Developer">
                    </div>
                </div>
                <div class="col-md-1 ">
                    <button class="btn btn-primary" name="search" type="submit">Search</button>
                </div>
            </div>
        </form>
        <br>


        <?php
        if (isset($searched)) {
            if (count($searched) == 1) {
                echo '<p>1 result</p>';
            } else if (count($searched) > 0) {
                echo '<p>' . count($searched) . ' results</p>';
            } else {
                echo '<p>No results</p>';
            }

            foreach ($searched as $search) {
                echo '
                    <div class="card mb-3">
                        <div class="card-header">
                            <b>'  . $search["first_name"] . ' ' . $search["last_name"] . '</b> (<a href="mailto:' . $search["email"] . '">' . $search["email"] . '</a>)' .  '
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2    ">';
                                    if ($search['picture']) {
                                        $base64Image = base64_encode($search['picture']);
                                        $pictureData = "data:image/jpeg;base64," . $base64Image;
                                        echo '<img src="' . $pictureData . '" alt="Profile picture of '.$search['first_name'].' '.$search['last_name'].'" class="img-fluid rounded border border-secondary">';
                                    } else {
                                        echo '<img src="../assets/img/user/default.jpg" alt="The default user profile picture" class="img-fluid rounded border border-secondary">';
                                    }
                echo '          </div>
                                <div class="col-md-10" id="vl">
                                    <p class="h5">Competence:</p>';
                                    if ($search['competence']) {
                                        echo '<p class="card-text">' . nl2br($search["competence"]) . '</p>';
                                    } else {
                                        echo '<p class="card-text">No competence written.</p>';
                                    }
                                    
                echo '          </div>
                            </div>
                        </div>
                    </div>
                    ';
            }
        }

        ?>
    </div>
</div>
<?php
}

makePage('display', 'User Search', $feedbackForUser, $feedbackColor, requireLogin: true);
