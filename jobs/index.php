<?php include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
 $dbhl = new DBHandlerListing();

function display() {
global $dbhl;

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
            <?php
           
            if ($listings = $dbhl->getAllListings()) {
                foreach($listings as $listing) {
                    echo'<div class="card mb-3">
                            <div class="card-header">
                                A Job Title
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        <h5 class="card-title">' . $listing["name"] . '</h5>
                                        <p class="card-text">' . $listing["description"] . '</p>
                                        <p class="card-text">' . $listing["job_category_id"] . '</p>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="listing.php?id=' . $listing['id'] . '" class="btn btn-primary">Se annonse</a>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                echo"No listings";
            }
            ?>
        </div>
    </div>
    <!-- Content here -->
<?php
}
makePage('display', 'Job listings');