<?php include_once 'assets/include/template.php';

function display() {
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Woops! (That's a 404)</h1>
        <p>The page you requested doesn't seem to exist. Are you sure you typed it correctly?</p>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Error 403');