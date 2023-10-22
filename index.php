<?php include_once 'assets/include/template.php';

function display() {
?>
<!-- Content here -->
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