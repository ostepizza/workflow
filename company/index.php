<?php include_once '../assets/include/template.php';

function display() {
?>
<!-- Content here -->
<!-- If not a part of a company -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Company dashboard</h1>
        <p>This section is used for companies intending to use this platform for creating job applications and finding new employees.</p>
        <p>You are currently not a member of a company. <a href="new.php">Create one</a>, or wait to be added to one.</p>
    </div>
</div>

<!-- If part of a company -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>Company dashboard - Company title</h1>
    </div>
</div>
<div class="row mt-5">
    <div class="col-md-7">
        <h2>Active job listings (Amount):</h2>
        <!-- TBD: sorted by deadline -->
        <hr>
        <div class="card">
            <div class="card-header">
                Job listing title (X views)
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <p>X applications received.</p>
                        <p>Deadline X-date</p>
                        <p>This listing was published X-date</p>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary w-100">View applications</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2">View listing</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-1">
    </div>

    <div class="col-md-3">
        <div class="row">
            <h2>Manage</h2>
            <hr>
            <button type="button" class="btn btn-primary w-100">Create new job listing</button>
            <button type="button" class="btn btn-secondary w-100 mt-2">View members of company</button>
            <button type="button" class="btn btn-secondary w-100 mt-2">Edit company</button>
        </div>
        <div class="row mt-3">
            <h2>Statistics</h2>
            <hr>
            <p>
                Total listings: X<br>
                Total applications received: X<br>
            </p>
        </div>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Companies', requireLogin: true);