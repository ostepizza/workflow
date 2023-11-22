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
           
            // Retrieve all listings from the database and assign them to $listings
            if ($listings = $dbhl->getAllActiveListings()) {
                // Loop through all listings and display them, if there are any
                foreach($listings as $listing) {
                    // Prepare for absolutely horrible echos
                    echo '<div class="card mb-3">
                            <div class="card-header">
                                <span class="h5">
                                    ' . $listing["name"];
                                    if ($listing['job_category_id'] != null) {
                                        echo ' <span class="badge bg-secondary">' . $listing["category_title"] . '</span>';
                                    }
                    echo        '</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <p class="card-text">
                                            <i>';
                                                if (strlen($listing["description"]) > 250) {
                                                    echo substr($listing["description"], 0, 250) . '...';
                                                } else {
                                                    echo $listing["description"];
                                                }   
                    echo '                  </i> 
                                        </p>
                                        <a class="btn btn-primary stretched-link" href="listing.php?id=' . $listing['id'] . '">Read more</a>
                                    </div>
                                    <div class="col-md-3">
                                        <p>
                                            Apply before<br>
                                            <b>' . date("D d. M. Y", strtotime($listing["deadline"])) . '</b>
                                        </p>
                                        <p>
                                            Posted by<br>
                                            <b>' . $listing["company_name"] . '</b> 
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                // Display a message if there are no listings
                echo '<p><span class="h2">There are currently no job listings available</span></p>';
            }
            ?>
        </div>
    </div>
    <!-- Content here -->
<?php
}
makePage('display', 'Job listings');