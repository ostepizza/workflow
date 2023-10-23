<?php include_once 'assets/include/template.php';

function display() {
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Woops! (That's a 403)</h1>
        <p>It looks like you were trying to access something you don't have the correct privileges for.</p>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Error 403');