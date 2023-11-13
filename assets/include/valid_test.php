<?php
include_once 'validation.php';

$validator = new Validator();

if (isset($_POST['submit'])) {
    $tos = (isset($_POST['acceptToS'])) ? $_POST['acceptToS'] : false;

    $validator->validateRegistration($tos, $_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName']);

    if($validator->valid) {
        echo 'true, proceed';
    } else {
        print_r($validator->feedback);
    }
}
?>
<form action="" method="post">
    <div class="form-group">
        <label for="email">Email address</label>
        <input type="text" id="email" name="email" class="form-control" placeholder="Enter your email" autofocus>
        <small class="form-text text-muted">This will be used to log you in</small>
    </div>

    <div class="form-group mt-3">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your desired password">
        <small class="form-text text-muted">Must be at least 8 characters long, and contain at least 1 number and 1 special character</small>
    </div>

    <div class="form-group mt-3">
        <label for="firstName">First name</label>
        <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Enter your first name">
    </div>

    <div class="form-group mt-3">
        <label for="lastName">Last name</label>
        <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Enter your last name">
    </div>

    <div class="form-group form-check mt-3">
        <input type="checkbox" id="acceptToS" name="acceptToS" class="form-check-input">
        <label class="form-check-label" for="acceptToS">I accept the <a href="#" data-bs-toggle="modal" data-bs-target=".modalToS">terms & conditions</a></label>
    </div>

    <button type="submit" id="submit" name="submit" class="btn btn-primary mt-3">Submit</button>
    
</form>