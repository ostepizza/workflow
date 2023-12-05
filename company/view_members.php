<?php include_once '../assets/include/template.php';

$feedbackForUser = NULL;
$feedbackColor = "danger";

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();
$dbhc = new DBHandlerCompany();

// Ask database if logged in member is found in the company_management table and retrieve company id if so
if ($companyId = $dbhc->getCompanyIdFromUserId($_SESSION['user_id'])){
    $companyDetails = $dbhc->getCompanyDetailsFromCompanyId($companyId);
    $companyName = $companyDetails['companyName'];
    $companyDescription = $companyDetails['companyDescription'];
} else {
    // Redirect if user isn't in a company
    header('Location: ../403.php');
}

if ($dbhc->isUserCompanySuperuser($_SESSION['user_id'])) {
    //Handle the add member form
    if (isset($_POST['addUserToCompany'])) {
        // Check if the new user field is valid
        $validator->validateEmail($_POST['email']);

        if($validator->valid) {

            // The email is a valid format, so check if the email belongs to an account
            if($dbhu->isEmailTaken($_POST['email'])) {
                if($dbhc->addNewUserToCompany($_POST['email'], $companyId)) {
                    $feedbackForUser .= 'User has been successfully added to the company.<br>';
                    $feedbackColor = 'success';
                } else {
                    $feedbackForUser .= 'User with email ' . $_POST['email'] . ' already belongs to a company.<br>';
                }
            } else {
                $feedbackForUser .= 'Email ' . $_POST['email'] . ' does not belong to a user.<br>';
            }

        } else {
            // If the email validation failed, tell the user what went wrong.
            $feedbackForUser = $validator->printAllFeedback();
        }
    }

    // Handle the toggle admin and remove user forms
    if (isset($_POST['user_id']) && isset($_POST['action'])) {
        //Make sure the user_id is an integer
        $userIdToManage = intval($_POST['user_id']);

        //Make sure the user isn't trying to manage themselves or someone who isn't a part of the company
        if($userIdToManage != $_SESSION['user_id'] && $dbhc->getCompanyIdFromUserId($userIdToManage) == $companyId) {

            if ($_POST['action'] == 'toggle_admin') {

                // Toggles the superuser-status of the user
                if ($dbhc->toggleUserSuperuser($companyId, $userIdToManage)) {
                    $feedbackForUser = 'User been successfully been promoted or demoted.';
                    $feedbackColor = 'success';
                } else {
                    $feedbackForUser = 'An error occurred.';
                    $feedbackColor = 'danger';
                }

            } else if ($_POST['action'] == 'remove') {

                // Removes the user from the company
                if ($dbhc->removeUserFromCompany($companyId, $userIdToManage)) {
                    $feedbackForUser = 'User been successfully been removed from the company.';
                    $feedbackColor = 'warning';
                } else {
                    $feedbackForUser = 'An error occurred.';
                    $feedbackColor = 'danger';
                }

            }
        } else {
            $feedbackForUser = 'You can not manage yourself or someone who is not a part of this company.';
            $feedbackColor = 'danger';
        }
    }
}

function display() {
global $companyId, $companyName, $companyDescription, $dbhc;

$superuser = $dbhc->isUserCompanySuperuser($_SESSION['user_id']);
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <a href="index.php"><button type="button" class="btn btn-secondary mb-3"><i class="fa-solid fa-circle-left"></i> Return to dashboard</button></a>
        <?php
        echo '<h1> ' . $companyName . ' members</h1>';

        //If user is a superuser, give them the possibility to add another member
        if ($superuser) {
            //Display the add member form
            echo '
            <form action="" method="post" class="row row-cols-lg-auto g-3 align-items-center">
                <div class="col-12">
                    <label class="visually-hidden" for="email">Email</label>
                    <div class="input-group">
                    <div class="input-group-text">Add member</div>
                    <input type="text" class="form-control" name="email" placeholder="Email">
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" id="addUserToCompany" name="addUserToCompany" class="btn btn-primary">Add user</button>
                </div>
            </form>
            ';
        }

        /*
            This looks chaotic, but bear with me as tables are a pain to work with beautifully in PHP.
            First, we retrieve all users from the company as an array.
            Then we loop through the array and display the users in a table, with "Admin" if the user being looped through is a superuser.
            If the user accessing the page is a superuser, we give them the possibility to make other users superusers or remove them from the company.
        */
        if ($users = $dbhc->retrieveAllCompanyUsers($companyId)) { ?>
            <table class="table table-hover table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">E-mail</th>
                        <th scope="col"></th>
                        <?php
                        if($superuser) {
                            echo '<th scope="col">Manage</th>';
                        }
                        ?>
                    <tr>
                </thead>
                <tbody>
            <?php
            foreach($users as $user) {
                if ($user['superuser'] == 1) {
                    $isAdmin = "Admin";
                } else {
                    $isAdmin = ""; 
                }
                echo '
                    <tr>
                        <td>' . $user['first_name'] . ' ' . $user['last_name'] . '</td>
                        <td><a href="mailto:' . $user['email'] . '">' . $user['email'] . '</a></td>
                        <td>' . $isAdmin . '</td>';
                if($superuser && $user['id'] != $_SESSION['user_id']) {
                    echo '<td>
                            <div class="d-flex">
                                <form method="POST" id="toggleadmin-' . $user['id'] . '">
                                    <input type="hidden" name="user_id" value="' . $user['id'] . '">
                                    <input type="hidden" name="action" value="toggle_admin">';
                    if ($user['superuser'] == 1) {
                        echo '<button type="submit" class="btn btn-secondary me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Demote to member"><i class="fa fa-arrow-circle-down"></i></button>';
                    } else {
                        echo '<button type="submit" class="btn btn-warning me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Make admin"><i class="fa fa-arrow-circle-up"></i></button>';
                    }
                    echo '  </form>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="' . $user['id'] . '">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove user from company"><i class="fa fa-times-circle"></i></button>
                                </form>
                            </div>
                        </td>';
                } else {
                    echo '<td></td>';
                }
                echo '</tr>';
            }
        }
        ?>  
            </tbody>
        </table>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'View company members', $feedbackForUser, $feedbackColor, requireLogin: true);