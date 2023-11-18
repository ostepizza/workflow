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
}

function display() {
global $companyId, $companyName, $companyDescription, $dbhc;

?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <a href="index.php"><button type="button" class="btn btn-secondary mb-3">&lt; Return to dashboard</button></a>
        <?php
        echo '<h1> ' . $companyName . ' members</h1>';

        //If superuser, have possibility to add another member
        if ($dbhc->isUserCompanySuperuser($_SESSION['user_id'])) {
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

        // REFACTORING HAS GOTTEN TO HERE
        // REFACTORING HAS GOTTEN TO HERE
        // REFACTORING HAS GOTTEN TO HERE
        // REFACTORING HAS GOTTEN TO HERE
        // REFACTORING HAS GOTTEN TO HERE

        //Retrieve company users
        $sql = 'SELECT u.first_name, u.last_name, u.email, cm.superuser
                FROM user u
                JOIN company_management cm ON u.id = cm.user_id
                JOIN company c ON c.id = cm.company_id
                WHERE c.id = ?
                ORDER BY cm.superuser DESC';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $company_id);
        if ($stmt->execute()) {

            echo '
            <table class="table table-hover table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th scope="col">First name</th>
                        <th scope="col">Last name</th>
                        <th scope="col">E-mail</th>
                        <th scope="col"></th>';
                        if($superuser) {
                            echo '<th scope="col">Manage</th>';
                        }
            echo    '<tr>
                </thead>';

            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['superuser'] == 1) {
                    $isAdmin = "Admin";
                } else {
                    $isAdmin = "";
                }
                echo '
                <tr>
                    <td>' . $row['first_name'] . '</td>
                    <td>' . $row['last_name'] . '</td>
                    <td><a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a></td>
                    <td>' . $isAdmin . '</td>';
                    if($superuser) {
                        echo '<td><button type="button" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Make admin"><i class="fa fa-arrow-circle-up"></i></button><button type="button" class="btn btn-danger"  data-bs-toggle="tooltip" data-bs-placement="top" title="Remove user from company"><i class="fa fa-times-circle"></i></button></td>';
                    }
        echo    '</tr>';
            }
        }
        echo '</table>';
        ?>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'View company members', $feedbackForUser, $feedbackColor, requireLogin: true);