<?php include_once '../assets/include/template.php';
/* 
    Edit a job application, which is only accessible to the user who created the application.
    It is accessed through a get request for the application id. 
    Necessary checks should be made to ensure that the user is the owner of the application.
*/
include_once '../assets/include/Validator.php';
$validator = new Validator();

include_once '../assets/include/DBHandler.php';
$dbha = new DBHandlerApplication();
$dbhc = new DBHandlerCompany();

//Set up emty variables for feedback
$feedbackForUser = NULL;
$feedbackColor = "danger";


if(isset($_GET["sentApplication"])) {
    $feedbackForUser .= 'Job application has been sent.<br>';
    $feedbackColor = 'success';
}

//Validation and GET request for when the send button is pressed
if (isset($_POST["applicationUpdate"])) {
    $validator->validateJobApplicationTitle($_POST["title"]);
    $validator->validateJobApplicationDescription($_POST["description"]);
    
    if ($validator->valid) {

        //TO-DO: get an update function for job applications (Values and current method used are only placeholders)
        if($dbha->createNewApplication(1, 1)){
            header("edit.php?sentApplication");
            exit();
        } else {
            echo "Something went wrong";
        }
    } else {
        $feedbackForUser = $validator->printAllFeedback();
        $feedbackColor = 'danger';
    }
}


function display()
{
?>
    <div class="row mt-5">
        <div class="col-md-4">
        <button class="btn btn-secondary">Go back</button>
        </div>
        <div class="col-md-4">
            <h1>Application to blabla</h1>
        </div>
    </div>
    <form action="" method="post">
    <div class="row mt-5">
        <div class="col-md-8">
            <div class="form-group">
                <input type="text" name="title" class="form-control" placeholder="..."<?php  ?>>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-8">
            <div class="form-group">
                <textarea class="form-control" name="description" rows="16" placeholder="Write here..."></textarea>
            </div>
        </div>
        <div class="col-md-1">
        </div>
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header text-center">
                    About employer
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">Sample Text</h5>
                    <p>Sample text</p>
                    <p>Sample text</p>
                    <p>Sample text</p>
                    <hr>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            By clicking this, you verify that you've read through the application atleast once. *
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Other terms that ive decided to make to a textbox >:D *
                        </label>
                    </div>
                    <hr>
                    <div class="col-md-7 mx-auto">
                        <button class="btn btn-primary" type="submit" name="applicationUpdate">Send application</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
}

makePage('display', 'Job listings');