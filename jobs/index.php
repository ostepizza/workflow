<?php include_once '../assets/include/template.php';

function display()
{
?>
    <!-- Content here -->
    <div class="row mt-5">
        <div class="col-md-4">
        </div>
        <div class="col-md-">
            <h1>Available job listings</h1>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-3">
            <!-- Filter section with search bar and checkboxes -->
            <div class="card mb-3">
                <div class="card-header">
                    <span class="h5">Filter</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputPassword1">Filter by words</label>
                        <input type="search" class="form-control" id="" placeholder="">
                    </div>
                    <hr>

                    <p>Filter by category:</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Technology
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Construction
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Something idk
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Something idk
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Something idk
                        </label>
                    </div>

                    <hr>
                    <button class="btn btn-primary">Search for jobs</button>
                </div>
            </div>
            <!-- End of filter box -->
        </div>
        <div class="col-md-1">
        </div>
        <div class="col-md-8">
            <!-- Start of cards representing job listings -->
            <div class="card mb-3">
                <div class="card-header">
                    A Job Title
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 class="card-title">Chalk</h5>
                            <p class="card-text">Konsulent.</p>
                            <p class="card-text">IT-Bransjen</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Se annonse</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    A Job Title
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 class="card-title">Selvskap A</h5>
                            <p class="card-text">Frontend Utvikler.</p>
                            <p class="card-text">IT og utvikling</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Se annonse</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-3">
                <div class="card-header">
                    A Job Title
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 class="card-title">Selvskap B</h5>
                            <p class="card-text">Frontend Utvikler.</p>
                            <p class="card-text">Job catagory</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Se annonse</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    A Job Title
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 class="card-title">Selvskap C</h5>
                            <p class="card-text">UI/UX designer og tester.</p>
                            <p class="card-text">UX</p>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Se annonse</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of job cards -->
        </div>
    </div>
    <!-- Content here -->
<?php
}

makePage('display', 'Job listings');
