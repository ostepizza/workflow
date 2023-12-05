<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();
$dbhc = new DBHandlerCompany();

// Include form input validator
include_once '../assets/include/Validator.php';
$validator = new Validator();

// Set up empty feedback
$feedbackForUser = NULL;
$feedbackColor = "danger";

// Retrieve the users info
if(!empty($_SESSION['user_id'])) {
    $userInfo = $dbhu->selectAllUserInfoByUserId($_SESSION['user_id']);
    $userInfo = array_merge($userInfo, $dbhc->getCompanyDetailsFromUserId($_SESSION['user_id']));
}

if (isset($_GET["updatedImage"])) {
    $feedbackForUser = 'Your profile picture has been successfully updated.<br>';
    $feedbackColor = 'success';
} else if (isset($_GET["updatedResume"])) {
    $feedbackForUser = 'Your resume has been successfully updated.<br>';
    $feedbackColor = 'success';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['imageToUpload']) && $_FILES['imageToUpload']['error'] == 0) {
        // Set up variables for the uploaded file
        $fileTmpPath = $_FILES['imageToUpload']['tmp_name'];
        $fileSize = $_FILES['imageToUpload']['size'];
        $fileType = mime_content_type($fileTmpPath);

        // Check if file is a jpg or png
        if ($fileType != 'image/jpeg' && $fileType != 'image/png') {
            $feedbackForUser = "Invalid file type. Only JPG and PNG types are accepted.<br>";
            $feedbackColor = "danger";
        } 
        // Check if file is under 10MB
        else if ($fileSize > 10485760) { // 10MB in bytes
            $feedbackForUser = "Image too large. Image must be less than 10MB.<br>";
            $feedbackColor = "danger";
        } 
        else {  
            // The picture is valid. First delete old image data (if there is any)
            if (!empty($userInfo['picture'])) {
                $oldImage = '../assets/img/user/' . $userInfo['picture'];
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            
            // Move the new image to the profile image directory
            $fileName = $_SESSION['user_id'] . '-' . $_FILES['imageToUpload']['name'];
            $destination = '../assets/img/user/' . $fileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                // If successfully moved, update the database
                if ($dbhu->updateProfileImage($_SESSION['user_id'], $fileName)) {
                    // If successfully updated DB, redirect to page with feedback
                    header('location: user_files.php?updatedImage');
                    exit();
                } else {
                    $feedbackForUser = "There was an error updating the database.<br>";
                    $feedbackColor = "danger";
                }

            } else {
                $feedbackForUser = "There was an error moving the uploaded image.<br>";
                $feedbackColor = "danger";
            }
        }
    } else {
        $feedbackForUser = "No image selected.<br>";
        $feedbackColor = "danger";
    }

    if (isset($_FILES['cvToUpload']) && $_FILES['cvToUpload']['error'] == 0) {
        // Set up variables for the uploaded file
        $fileTmpPath = $_FILES['cvToUpload']['tmp_name'];
        $fileSize = $_FILES['cvToUpload']['size'];
        $fileType = mime_content_type($fileTmpPath);

        // Check if file is a PDF
        if ($fileType != 'application/pdf') {
            $feedbackForUser = "Invalid file type. Only PDF types are accepted.<br>";
            $feedbackColor = "danger";
        } 
        // Check if file is under 20MB
        else if ($fileSize > 20971520) { // 20MB in bytes
            $feedbackForUser.= "File too large. File must be less than 20MB.<br>";
            $feedbackColor = "danger";
        } 
        else {
            // The file is valid. First delete old file data (if there is any)
            if (!empty($userInfo['cv'])) {
                $oldFile = '../assets/pdf/user/' . $userInfo['cv'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            // Move the new file to the CV directory
            $fileName = $_SESSION['user_id'] . '-' . $_FILES['cvToUpload']['name'];
            $destination = '../assets/pdf/user/' . $fileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                // If successfully moved, update the database
                if ($dbhu->updateUserCV($_SESSION['user_id'], $fileName)) {
                    // If successfully updated DB, redirect to page with feedback
                    header('location: user_files.php?updatedResume');
                    exit();
                } else {
                    $feedbackForUser = "There was an error updating the database.<br>";
                    $feedbackColor = "danger";
                }
        
            } else {
                $feedbackForUser = "There was an error moving the uploaded file.<br>";
                $feedbackColor = "danger";
            }
        }

    } else {
        $feedbackForUser = "No resume selected.<br>";
        $feedbackColor = "danger";
    }
}

function display() {
global $userInfo;
?>
<a class="btn btn-secondary mt-3" href="index.php">Go back to profile</a>

<div class="row mt-3">
    <h1>Upload user files</h1>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <h2>Profile picture</h2>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <?php
                if (isset($userInfo['picture'])) {
                    echo '<img src="../assets/img/user/' . $userInfo['picture'] . '" class="img-fluid rounded border border-secondary" alt="Your profile picture">';
                } else {
                    echo '<img src="../assets/img/user/default.jpg" class="img-fluid rounded border border-secondary" alt="The default profile picture">';
                }
                ?>
                <br>
                <form action="" method="post" enctype="multipart/form-data">
                    <br>
                    Select image to upload:
                    <input type="file" name="imageToUpload" id="imageToUpload" class="w-100 p-3 rounded border border-secondary mb-3">
                    <br>
                    <input type="submit" value="Upload Image" name="submit" class="btn btn-primary w-100">
                </form>
            </div>
            <div class="col-md-6">
            Allowed file types for profile picture:<br>
            - JPG<br>
            - PNG<br>
            Max size is 10MB.
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <h2>Resume</h2>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <?php
                if (isset($userInfo['cv'])) {
                    echo 'You have already uploaded your resume. Uploading a new one will overwrite the old.<br>';
                    echo '<a href="../assets/pdf/user/' . $userInfo['cv'] . '" class="btn btn-primary w-100" role="button" target=”_blank”>View resume</a>';
                } else {
                    echo 'You have not yet uploaded a resume.<br>';
                }
                ?>
                <br>
                <hr>
                <br>
                <form action="" method="post" enctype="multipart/form-data">
                    Select file to upload:
                    <input type="file" name="cvToUpload" id="cvToUpload" class="w-100 p-3 rounded border border-secondary mb-3">
                    <br>
                    <input type="submit" value="Upload File" name="submit" class="btn btn-primary w-100">
                </form>
            </div>
            <div class="col-md-6">
            Allowed file types for resume:<br>
            - PDF<br>
            Max size is 20MB.
            </div>
        </div>
    </div>
</div>
<?php
}

makePage('display', 'Profile', $feedbackForUser, $feedbackColor, requireLogin: true);