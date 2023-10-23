<?php include_once 'assets/include/template.php';

function display() {
?>
<!-- Content here -->
<?php
if(isset($_GET['loggedout'])) {
    echo('
    <div class="alert alert-warning mt-3" role="alert">
        You have been successfully logged out. 
    </div>
    ');
} else if (isset($_GET['loggedin'])) {
    echo('
    <div class="alert alert-success mt-3" role="alert">
        You have been successfully logged in. 
    </div>
    ');
}
?>
<div class="row mt-5">
    <div class="col-md-7">
        <h1>There be text</h1>
    </div>
    
    <div class="col-md-2">
    </div>
    
    <div class="col-md-3">
        <h2>News</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Something to check out</h5>
                <p class="card-text">Veldig kult.</p>
                <a href="#" class="btn btn-primary">Knapp</a>
            </div>
        </div>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Home');