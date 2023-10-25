<?php include_once '../assets/include/template.php';

include("../assets/include/connection.php");
$conn = createDBConnection(); // Connects to the database

$feedbackForUser = NULL;
$feedbackColor = "danger";

$user_id = $_SESSION['user_id'];

// Ask database if logged in member is found in the company_management table
$sql = 'SELECT `company_id` FROM `company_management` WHERE `user_id` = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
if ($stmt->execute()) {
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If any rows are found, the user is a part of a company
        // Redirect to the 403, since users are only supposed to make max 1 company
        $stmt->close();
        header('Location: ../403.php');
    }
    $stmt->close();
}

if (isset($_POST['submit'])) {
    $allConditionsMet = true; // Sets up a fail condition if user-input is bad

    if (empty($_POST['name'])) {
        // Check if company name is empty
        $feedbackForUser .= "You need to enter a company name.<br>";
        $allConditionsMet = false;
    } else if (strlen($_POST['name']) > 100) {
        $feedbackForUser .= "Your company name can not exceed 100 characters.<br>";
        $allConditionsMet = false;
    }

    if (!empty($_POST['description'])) {
        // Check the email before sending a query to SQL server to reduce query attempts
        if (strlen($_POST['description']) > 500) {
            $feedbackForUser .= 'Description needs to be shorter than 500 characters.<br>';
            $allConditionsMet = false;
        }
    }

    if ($allConditionsMet) {
        // Get variables needed for the SQL statements
        $name = $_POST['name'];
        $description = $_POST['description'];

        // Try to find the name provided in the form in the database
        $sql = 'SELECT `name` FROM `company` WHERE `name` = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $name);
        if ($stmt->execute()) {
            $stmt->store_result();
    
            if ($stmt->num_rows == 1) {
                // If we get a row, compare the names in lower case - just to be sure
                $stmt->bind_result($result_name);
                $stmt->fetch();

                if (strtolower($name) == strtolower($result_name)) {
                    $feedbackForUser = "Company with this name already exists!";
                } 

                $stmt->close();
            } else if ($stmt->num_rows < 1) {
                // No existing companies were found, so the magic begins here
                // First we make sure the statement is closed
                $stmt->close();
                
                // Then we start over, and insert the company name and description into the database
                $sql = 'INSERT INTO `company` (`id`, `name`, `description`) VALUES (NULL, ?, ?)';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $name, $description);
                
                if ($stmt->execute()) {
                    // If it executes, close the statement and start with the second part
                    $stmt->close();

                    // Retrieve the ID of the newly inserted company
                    $company_id = $conn->insert_id;

                    // Then insert the user id and company id in the company_management table
                    // This table is used to store data about who has access to what company, and with what permissions
                    // With the below SQL statement the user registering the company becomes a part of it automatically, with elevated privileges
                    $sql = 'INSERT INTO `company_management` (`user_id`, `company_id`, `superuser`) VALUES (?, ?, 1)';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ii', $user_id, $company_id);

                    if ($stmt->execute()) {
                        // If all of these SQL statements have been executed successfully,
                        // the company is now properly stored in the database, with correct
                        // references to their proper foreign keys
                        $stmt->close();
                        header('Location: index.php?registerSuccess');
                    } else {
                        $feedbackForUser = "An error occurred while creating a company.<br>";
                    }
                } else {
                        $feedbackForUser = "An error occurred while creating a company.<br>";
                }
            } else {
                $feedbackForUser = "An error occurred.";
            }
        }
    }
}

function display() {
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <a href="index.php"><button type="button" class="btn btn-secondary mb-3">&lt; Return to dashboard</button></a>
        <h1>New company</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Company name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your company name" autofocus>
            </div>

            <div class="form-group mt-3">
                <label for="description">Company description (optional, max 500 characters)</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Write a quick description about what your company does"></textarea>
            </div>

            <button type="submit" id="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'New company', $feedbackForUser, $feedbackColor, requireLogin: true);