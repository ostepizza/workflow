<?php include_once '../assets/include/template.php';

$feedbackForUser = NULL;
$feedbackColor = "danger";

include("../assets/include/connection.php");
$conn = createDBConnection(); // Connects to the database
$user_id = $_SESSION['user_id'];

// Ask database if logged in member is found in the company_management table
$sql = 'SELECT `company_id`, `superuser` FROM `company_management` WHERE `user_id` = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
if ($stmt->execute()) {
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        // If one row is found, the user is a part of a company
        $stmt->bind_result($company_id, $superuser);
        $stmt->fetch();
        $stmt->close();

        if($superuser == 1) {
            //If the user is a superuser
            //Connect to database and retrieve existing data, like company name
            $sql = 'SELECT `name`, `description` FROM `company` WHERE `id` = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $company_id);
            if ($stmt->execute()) {
                $stmt->store_result();
        
                if ($stmt->num_rows == 1) {
                    //Get company name and description for populating form inputs and later comparisons
                    $stmt->bind_result($company_name, $company_description);
                    $stmt->fetch();
                } else {
                    $feedbackForUser = "An error occurred while retrieving company data.<br>";
                }
            }
        } else {
            //Redirect if user is not a superuser
            header('Location: ../403.php');
        }
    } else {
        //Redirect if user not in a company
        $stmt->close();
        header('Location: ../403.php');
    }
}

//Handle the two forms available on the page
if (isset($_POST['companyUpdate'])) {
    //If the user has pressed the update button
    echo "Form 1 submitted";
    if (!empty($_POST['name'])) {
        if($_POST['name'] != $company_name) {
            echo 'name will be updated';
        } else {
            echo 'name will not be updated';
        }
    } else {
        //Error: Name can not be empty
    }

    if (!empty($_POST['description'])) {
        if($_POST['description'] != $company_description) {
            echo 'description will be updated';
        } else {
            echo 'description will not be updated';
        }
    }
} else if (isset($_POST['companyDelete'])) {
    //At this point, the user has both been authenticated as being a member of the company, and a superuser for the company.
    //We can therefore continue with the deletion attempt with no further questioning regarding company membership.

    $continueDeletion = true;
    $feedbackForUser;

    //Check both checkmark and password field
    //Don't attempt to continue deletion if either isn't filled
    if(!isset($_POST['deleteCheckmark'])){
        $continueDeletion = false;
        $feedbackForUser .= "You need to check the checkbox in order to initiate company deletion.<br>";
    }

    if(empty($_POST['currentPassword'])){
        $continueDeletion = false;
        $feedbackForUser .= "You need to confirm your password in order to initiate company deletion.<br>";
    }

    //If password field and checkmark is checked, we can start doing some checks to see if all criterias are met
    //If all criterias are met, we delete the company
    if($continueDeletion == true) {
        //Retrieve users hashed password
        $sql = 'SELECT `password` FROM `user` WHERE `id` = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                //User found
                $stmt->bind_result($hashed_password);
                $stmt->fetch();

                //Compare user input with stored hashed password
                //If it's the same, delete the company
                if(password_verify($_POST['currentPassword'], $hashed_password)) {
                    $sql = 'DELETE FROM `company` WHERE `id` = ?';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $company_id);
                    if ($stmt->execute()) {
                        //Redirect if successfully deleted
                        header('Location: index.php?deletedCompany');
                    } else {
                        $feedbackForUser .= "An error occurred while attempting to delete the company.<br>";
                    }
                } else {
                    $feedbackForUser .= "Wrong password input. Company is not deleted.<br>";
                }
            } else {
                $feedbackForUser .= "An error occurred while retrieving user data.<br>";
            }
        }
    }
}

function display() {
global $company_name, $company_description;

?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Edit company <a href="#" data-bs-toggle="modal" data-bs-target=".modalDeleteCompany"><button type="button" class="btn btn-danger">Delete company</button></a></h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Company name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo $company_name; ?>" autofocus>
            </div>

            <div class="form-group mt-3">
                <label for="description">Company description (optional, max 500 characters)</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $company_description; ?></textarea>
            </div>

            <button type="submit" id="submit" name="companyUpdate" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>
</div>

<div class="modal fade modalDeleteCompany" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete company</h5>
            </div>
            <div class="modal-body">
                <p><span class="text-danger"><b>WARNING! This is a destructive action!</b></span></p>
                <p>By proceeding, the company "<?php echo $company_name; ?>" will be deleted. With this action, all related job listings and user submitted job applications will also be deleted. This action <b>CAN NOT BE REVERSED.</b></p>
                <br>
                <p><b>If you still wish to proceed, please check the checkbox below and input your current password:</b></p>
            </div>
            <div class="modal-footer">
                <form action="" method="post" class="row row-cols-lg-auto align-items-center">
                    <div class="col-12">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="deleteCheckmark">
                        <label class="form-check-label" for="inlineFormCheck">
                            I confirm that I wish to delete the company
                        </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <input type="password" class="form-control" name="currentPassword" placeholder="Current password">
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-danger" name="companyDelete">Delete</button>
                    </div>
                </form>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
  </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Edit company', $feedbackForUser, $feedbackColor, requireLogin: true);