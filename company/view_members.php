<?php include_once '../assets/include/template.php';

$feedbackForUser = NULL;
$feedbackColor = "danger";

include("../assets/include/connection.php");
$conn = createDBConnection(); // Connects to the database
$user_id = $_SESSION['user_id'];

// Ask database if logged in member is found in the company_management table
$sql = 'SELECT cm.company_id, cm.superuser, c.name AS company_name
    FROM company_management cm
    JOIN company c ON c.id = cm.company_id
    WHERE cm.user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
if ($stmt->execute()) {
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        // If one row is found, the user is a part of a company
        // Store the company id and if the user is a superuser for later
        $stmt->bind_result($company_id, $superuser, $company_name);
        $stmt->fetch();
        $stmt->close();
    } else {
        //Redirect if user not in a company
        $stmt->close();
        header('Location: ../403.php');
    }
}

if($superuser = true) {
    //Handle the add member form
    if (isset($_POST['addUserToCompany'])) {
        //If the user has pressed the update button
        if (!empty($_POST['email'])) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                //Check if email belongs to user
                $sql = 'SELECT `id` FROM `user` WHERE `email` = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $_POST['email']);
                if ($stmt->execute()) {
                    $stmt->store_result();
            
                    if ($stmt->num_rows == 1) {
                        //If email belongs to a user, check if user is member of another company
                        //Start with binding and closing statement from earlier
                        $stmt->bind_result($user_id_to_add);
                        $stmt->fetch();
                        $stmt->close();

                        //Check if user_id is present in company_management table, and if so, retrieve company id as well
                        $sql = 'SELECT `user_id` FROM `company_management` WHERE `user_id` = ?';
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $user_id_to_add);
                        if ($stmt->execute()) {
                            $stmt->store_result();
                            if ($stmt->num_rows == 0) {
                                //user is not present in table, and can be added
                                $sql = 'INSERT INTO `company_management` (`user_id`, `company_id`, `superuser`) VALUES (?, ?, 0)';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('ii', $user_id_to_add, $company_id);
                                if ($stmt->execute()) {
                                    $feedbackForUser .= 'User has been successfully added to the company.<br>';
                                    $feedbackColor = 'success';
                                } else {
                                    $feedbackForUser .= 'An error occurred while adding the user to the company.<br>';
                                }
                            } else if ($stmt->num_rows == 1) {
                                $feedbackForUser .= 'User ' . $_POST['email'] . ' is already a member of a company.<br>';
                            }  
                        } else {
                            $feedbackForUser .= 'An error occurred.<br>';
                        }                 
                    } else if ($stmt->num_rows == 0) {
                        $feedbackForUser .= 'Email ' . $_POST['email'] . ' does not belong to a user.<br>';
                    } else {
                        $feedbackForUser .= 'An error occurred.<br>';
                    }
                } else {
                    $feedbackForUser .= 'An error occurred.<br>';
                }
            } else {
                $feedbackForUser .= $_POST['email'] . ' is not a valid email.<br>';
            }
        } else {
            $feedbackForUser .= "Email can not be empty.<br>";
        }
    }
}

function display() {
global $company_id, $company_name, $company_description, $conn;

?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <a href="index.php"><button type="button" class="btn btn-secondary mb-3">&lt; Return to dashboard</button></a>
        <?php
        echo '<h1> ' . $company_name . ' members</h1>';

        //If superuser, have possibility to add another member
        if ($superuser = true) {
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
                    <button type="submit" id="submit" name="addUserToCompany" class="btn btn-primary">Add user</button>
                </div>
            </form>
            ';
        }

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